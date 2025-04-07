import sys

x = int(sys.argv[1])
y = int(sys.argv[2])

# Moitié des cas justes, moitié faux (selon tes tests définis)
if x > 0 and y > 0:
    print(x + y)
else:
    print(x * y)  # volontairement incorrect
