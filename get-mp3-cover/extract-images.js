const mm = require('music-metadata');
const fs = require('fs');
const path = require('path');

const folderPath = "D:/Downloads from telegram/sss";
const imageFolder = path.join(folderPath, 'images');

if (!fs.existsSync(imageFolder)) fs.mkdirSync(imageFolder);

fs.readdir(folderPath, async (err, files) => {
    if (err) return console.error(err);

    const mp3Files = files.filter(f => f.endsWith('.mp3'));

    for (const [i, file] of mp3Files.entries()) {
        const filePath = path.join(folderPath, file);
        try {
            const metadata = await mm.parseFile(filePath);
            const pictures = metadata.common.picture;

            if (!pictures || pictures.length === 0) {
                console.log(`[${i + 1}] No image found in: ${file}`);
                continue;
            }

            // Take first image
            const pic = pictures[0];
            const ext = pic.format === 'image/png' ? '.png' : '.jpg';
            const imagePath = path.join(imageFolder, path.basename(file, '.mp3') + ext);

            fs.writeFileSync(imagePath, pic.data);
            console.log(`[${i + 1}] Extracted image to: images/${path.basename(imagePath)}`);
        } catch (e) {
            console.error(`[${i + 1}] Error processing ${file}:`, e.message);
        }
    }
});
