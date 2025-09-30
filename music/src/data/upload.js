// data/upload.js

export async function uploadSingleAudio(artist, audio) {
    const response = await fetch('http://127.0.0.1:8000/api/upload-audios', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            artist,
            audios: [audio], // send one audio at a time
        }),
    });

    const result = await response.json();
    console.log('✅ Uploaded:', result);
    return result;
}
