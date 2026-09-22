from pathlib import Path

path = Path("ai/data/complaint_training.json")

text = path.read_text(encoding="utf-8")

result = []
inside_string = False
escaped = False

for char in text:
    if inside_string:
        if escaped:
            result.append(char)
            escaped = False
            continue

        if char == "\\":
            result.append(char)
            escaped = True
            continue

        if char == '"':
            result.append(char)
            inside_string = False
            continue

        if char in "\r\n":
            # Replace accidental physical line breaks
            # inside JSON strings with a space.
            if not result or result[-1] != " ":
                result.append(" ")
            continue

        result.append(char)

    else:
        result.append(char)

        if char == '"':
            inside_string = True

fixed = "".join(result)

path.write_text(fixed, encoding="utf-8")

print("JSON cleanup completed.")
print(f"File updated: {path}")