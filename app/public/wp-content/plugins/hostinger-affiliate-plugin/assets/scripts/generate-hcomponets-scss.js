const fs = require('fs');
const path = require('path');

const sourcePath = path.join(__dirname, '../node_modules/@hostinger/hcomponents/dist/style.css');
const targetDir = path.join(__dirname, '../src/backend/styles');
const targetPath = path.join(targetDir, 'hcomponents.scss');

// Check if the target file already exists
fs.access(targetPath, fs.constants.F_OK, (err) => {
  if (!err) {
    console.log('hcomponents.scss file already exists, no action taken.');

    return;
  }

  // Read the source file
  fs.readFile(sourcePath, 'utf8', (err, data) => {
    if (err) {
      console.error('Error reading source file:', err);

      return;
    }

    // Replace :root with *
    const modifiedData = data.replace(/:root/g, '*');

    // Ensure the target directory exists
    fs.mkdir(targetDir, { recursive: true }, (err) => {
      if (err) {
        console.error('Error creating target directory:', err);

        return;
      }

      // Write the modified content to the target file
      fs.writeFile(targetPath, modifiedData, 'utf8', (err) => {
        if (err) {
          console.error('Error writing to target file:', err);
        } else {
          console.log('File copied, modified, and renamed to hcomponents.scss successfully');
        }
      });
    });
  });
});
