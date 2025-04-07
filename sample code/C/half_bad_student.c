#include <stdio.h>
#include <stdlib.h>

int main(int argc, char *argv[]) {
    int x = atoi(argv[1]);
    int y = atoi(argv[2]);

    if (x > 0 && y > 0) {
        printf("%d\n", x + y);  // bon
    } else {
        printf("%d\n", x * y);  // faux
    }

    return 0;
}
