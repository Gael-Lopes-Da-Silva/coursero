#!/bin/bash

cd /var/www/html/coursero/script

DB_USER="coursero"
DB_PASS="coursero"
DB_NAME="coursero"

UPLOADS_BASE="../uploads"
TMP_DIR="/tmp/coursero_check"
mkdir -p $TMP_DIR

# Récupérer les soumissions "pending"
submissions=$(mysql -u$DB_USER -p$DB_PASS -D$DB_NAME -N -e \
"SELECT s.id, s.exercise_id, s.file_path, e.reference_file, s.language FROM submissions s JOIN exercises e ON s.exercise_id = e.id WHERE s.status = 'pending';")

while IFS=$'\t' read -r submission_id exercise_id student_file ref_file language; do
    echo "Processing submission ID $submission_id (exercise $exercise_id)..."

    student_path="$UPLOADS_BASE/submissions/$(basename "$student_file")"
    ref_path="$UPLOADS_BASE/exercises/references/$(basename "$ref_file")"
    test_json="../tests/${exercise_id}.json"

    # Vérification des fichiers nécessaires
    if [[ ! -f "$student_path" || ! -f "$ref_path" || ! -f "$test_json" ]]; then
        echo "Fichiers manquants pour l'exercice $exercise_id. Passage au suivant."
        continue
    fi

    # Mettre à jour le statut à "running"
    mysql -u$DB_USER -p$DB_PASS -D$DB_NAME -e \
    "UPDATE submissions SET status = 'running' WHERE id = $submission_id;"

    args_list=$(jq -c '.args[]' "$test_json")
    pass=0
    total=0

    # Python
    if [[ "$language" == "Python" ]]; then
        for args in $args_list; do
            clean_args=$(echo $args | jq -r '. | join(" ")')
            ref_out=$(python3 "$ref_path" $clean_args 2>/dev/null | xargs)
            sub_out=$(python3 "$student_path" $clean_args 2>/dev/null | xargs)

            if [[ "$ref_out" == "$sub_out" ]]; then
                ((pass++))
            fi
            ((total++))
        done

    # C
    elif [[ "$language" == "C" ]]; then
        gcc "$ref_path" -o "$TMP_DIR/ref_bin" 2>/dev/null
        gcc "$student_path" -o "$TMP_DIR/sub_bin" 2>/dev/null

        for args in $args_list; do
            clean_args=$(echo $args | jq -r '. | join(" ")')
            ref_out=$($TMP_DIR/ref_bin $clean_args 2>/dev/null)
            sub_out=$($TMP_DIR/sub_bin $clean_args 2>/dev/null)

            if [[ "$ref_out" == "$sub_out" ]]; then
                ((pass++))
            fi
            ((total++))
        done
        rm -f $TMP_DIR/ref_bin $TMP_DIR/sub_bin
    fi

    score=0
    if [[ $total -gt 0 ]]; then
        score=$(( (100 * pass) / total ))
    fi

    echo "Submission $submission_id score: $score%"

    # Mise à jour du score et statut
    mysql -u$DB_USER -p$DB_PASS -D$DB_NAME -e \
    "UPDATE submissions SET status = 'done', score = $score WHERE id = $submission_id;"

done <<< "$submissions"
