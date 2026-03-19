const fs = require('fs');
const path = require('path');

const dir = 'resources/views/admin';

const replacements = [
    ['bg-dark', 'bg-white'],
    ['bg-black', 'bg-light'],
    ['text-white-50', 'text-muted'],
    ['text-while-50', 'text-muted'],
    ['text-white', 'text-dark'],
    ['text-info', 'text-primary'],
    ['text-warning', 'text-primary'],
    ['border-secondary', 'border-light'],
    ['border-info', 'border-primary'],
    ['border-warning', 'border-primary'],
    ['btn-info', 'btn-primary'],
    ['btn-outline-info', 'btn-outline-primary'],
    ['btn-warning', 'btn-primary'],
    ['btn-outline-warning', 'btn-outline-primary'],
    ['bg-info', 'bg-primary'],
    ['bg-warning', 'bg-primary'],
    ['#1a1d20', '#ffffff'],
    ['#212529', '#f8f9fa'],
    ['#0dcaf0', '#0d6efd'],
    ['#ffc107', '#0d6efd']
];

function walkSync(currentDirPath, callback) {
    fs.readdirSync(currentDirPath).forEach(function (name) {
        var filePath = path.join(currentDirPath, name);
        var stat = fs.statSync(filePath);
        if (stat.isFile()) {
            callback(filePath, stat);
        } else if (stat.isDirectory()) {
            walkSync(filePath, callback);
        }
    });
}

let count = 0;

walkSync(dir, function(filePath) {
    if (filePath.endsWith('.php')) {
        let content = fs.readFileSync(filePath, 'utf8');
        let original = content;

        content = content.replace(/table-dark/g, '');

        replacements.forEach(([search, replace]) => {
            content = content.split(search).join(replace);
        });

        content = content.split('bg-opacity-25').join('bg-opacity-10');

        if (original !== content) {
            fs.writeFileSync(filePath, content, 'utf8');
            console.log(`Updated: ${filePath}`);
            count++;
        }
    }
});

console.log(`Total files updated: ${count}`);
