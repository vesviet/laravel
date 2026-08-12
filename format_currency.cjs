const fs = require('fs');
const path = require('path');

function processFile(filepath) {
    let content = fs.readFileSync(filepath, 'utf-8');
    
    const pattern1 = /\$\{\{\s*number_format\(([^,]+),\s*2\)\s*\}\}/g;
    const replacement1 = '{{ number_format($1, 0, \',\', \'.\') }}₫';
    
    const pattern2 = /-\$\{\{\s*number_format\(([^,]+),\s*2\)\s*\}\}/g;
    const replacement2 = '-{{ number_format($1, 0, \',\', \'.\') }}₫';
    
    content = content.replace(pattern1, replacement1);
    content = content.replace(pattern2, replacement2);
    
    fs.writeFileSync(filepath, content, 'utf-8');
}

function walkDir(dir) {
    fs.readdirSync(dir).forEach(file => {
        let fullPath = path.join(dir, file);
        if (fs.lstatSync(fullPath).isDirectory()) {
            walkDir(fullPath);
        } else if (fullPath.endsWith('.blade.php')) {
            processFile(fullPath);
        }
    });
}

walkDir('D:/myproject/laravel/resources/views');
console.log('Currency formatted successfully.');
