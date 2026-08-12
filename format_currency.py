import os
import re

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Regex to match: ${{ number_format($variable, 2) }}
    # We want to replace it with: {{ number_format($variable, 0, ',', '.') }}₫
    # Also handle cases where it might not have the $ sign in front, just in case.
    
    # 1. Replace ${{ number_format(..., 2) }} -> {{ number_format(..., 0, ',', '.') }}₫
    pattern1 = r'\$\{\{\s*number_format\(([^,]+),\s*2\)\s*\}\}'
    replacement1 = r'{{ number_format(\1, 0, \',\', \'.\') }}₫'
    content = re.sub(pattern1, replacement1, content)
    
    # 2. Replace -${{ number_format(..., 2) }} -> -{{ number_format(..., 0, ',', '.') }}₫
    pattern2 = r'-\$\{\{\s*number_format\(([^,]+),\s*2\)\s*\}\}'
    replacement2 = r'-{{ number_format(\1, 0, \',\', \'.\') }}₫'
    content = re.sub(pattern2, replacement2, content)
    
    # 3. Replace ${{ number_format($this->subtotal, 2) }} -> {{ number_format($this->subtotal, 0, ',', '.') }}₫
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

def main():
    views_dir = 'D:/myproject/laravel/resources/views'
    for root, dirs, files in os.walk(views_dir):
        for file in files:
            if file.endswith('.blade.php'):
                filepath = os.path.join(root, file)
                process_file(filepath)

if __name__ == '__main__':
    main()
