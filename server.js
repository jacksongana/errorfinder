const http = require('http');
const fs = require('fs');
const path = require('path');

const server = http.createServer((req, res) => {
  const filePath = path.join(__dirname, req.url);

  // Si c'est un fichier PHP
  if (filePath.endsWith('.php')) {
    const php = require('child_process').spawn('php-cgi', ['-f', filePath]);

    php.stdout.on('data', (data) => {
      res.write(data);
    });

    php.stderr.on('data', (data) => {
      res.write(`Erreur PHP: ${data}`);
    });

    php.on('close', () => {
      res.end();
    });
  } else {
    fs.readFile(filePath, 'utf-8', (err, data) => {
      if (err) {
        res.writeHead(404, { 'Content-Type': 'text/plain' });
        res.write('Page non trouvée');
        res.end();
      } else {
        res.writeHead(200, { 'Content-Type': 'text/html' });
        res.write(data);
        res.end();
      }
    });
  }
});

server.listen(3000, () => {
  console.log('Serveur Node.js en écoute sur http://localhost:3000');
});
