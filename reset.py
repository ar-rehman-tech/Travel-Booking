import os, re

def restore(filepath, content):
    # Remove the line numbers and colon prefix "123: "
    lines = content.split('\n')
    cleaned_lines = []
    for line in lines:
        cleaned_lines.append(re.sub(r'^\d+:\s', '', line))
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write('\n'.join(cleaned_lines))

print("Script ready.")
