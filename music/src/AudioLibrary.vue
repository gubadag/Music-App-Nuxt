<template>
    <div class="artist-list">
        <h1>🎧 Artist Library</h1>

        <button @click="uploadAllArtists" style="margin-bottom: 20px;">
            ⬆ Upload All Artists to Cloudflare R2
        </button>

        <ul>
            <li
                v-for="(artist, index) in artists"
                :key="index"
                @click="selectArtist(index)"
                class="artist-link"
            >
                {{ artist.name }}
            </li>
        </ul>

        <div v-if="selectedArtist">
            <h2>{{ selectedArtist.name }}'s Songs</h2>

            <div
                v-for="(audio, index) in selectedArtist.audios"
                :key="index"
                class="eventContent__audio"
            >
                <span class="eventContent__audio-title">{{ audio.title }}</span>
                <span class="eventContent__audio-author">{{ audio.author }}</span>
                <span class="eventContent__audio-dur">{{ audio.durationText }}</span>

                <button @click="togglePlay(index)">
                    {{ currentAudioIndex === index && isPlaying ? '⏸ Pause' : '▶ Play' }}
                </button>

                <!-- Copy button with dynamic background -->
                <button
                    :class="{ copied: copiedIndex === index }"
                    @click="copySrc(audio.src, index)"
                >
                    📋 Copy
                </button>

                <audio ref="audio" :src="audio.src" @ended="onEnded" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue'
import * as Arobi from './data/audios.js'
import * as Iska from './data/audiosIska.js'
import * as SykeIska from './data/audiosSykeIska.js'
import * as Zumer from './data/audiosZumer.js'

const artists = ref([
    { name: Arobi.artist, audios: Arobi.audios },
    { name: Iska.artist, audios: Iska.audios },
    { name: SykeIska.artist, audios: SykeIska.audios },
    { name: Zumer.artist, audios: Zumer.audios },
])

const selectedArtist = ref(null)
const audioRefs = ref([])
const currentAudioIndex = ref(null)
const isPlaying = ref(false)

// Track the index of the last copied song
const copiedIndex = ref(null)

async function uploadAllArtists() {
    alert('✅ All uploads completed!')
}

function selectArtist(index) {
    selectedArtist.value = artists.value[index]
    currentAudioIndex.value = null
    isPlaying.value = false

    nextTick(() => {
        audioRefs.value = document.querySelectorAll('audio')
    })

    // reset copiedIndex when changing artist
    copiedIndex.value = null
}

async function togglePlay(index) {
    await nextTick()
    const selectedAudio = audioRefs.value[index]
    if (!selectedAudio) return

    if (currentAudioIndex.value !== null && currentAudioIndex.value !== index) {
        const currentAudio = audioRefs.value[currentAudioIndex.value]
        if (currentAudio) {
            currentAudio.pause()
            currentAudio.currentTime = 0
        }
    }

    if (currentAudioIndex.value === index && isPlaying.value) {
        selectedAudio.pause()
        isPlaying.value = false
    } else {
        selectedAudio.play()
        currentAudioIndex.value = index
        isPlaying.value = true
    }
}

function onEnded() {
    isPlaying.value = false
    currentAudioIndex.value = null
}

// Copy function sets copiedIndex to change button style
function copySrc(src, index) {
    navigator.clipboard.writeText(src).then(() => {
        copiedIndex.value = index
    }).catch(() => {
        // silently fail, no alert
    })
}
</script>

<style scoped>
/* ... existing styles ... */

button.copied {
    background-color: black !important;
    color: white !important;
}




.artist-list {
    padding: 20px;
}
.artist-link {
    cursor: pointer;
    margin-bottom: 10px;
    color: blue;
    text-decoration: underline;
}
.eventContent__audio {
    margin-top: 10px;
    border-top: 1px solid #ccc;
    padding-top: 10px;
}




/* Base container */
.artist-list {
    max-width: 900px;
    margin: 40px auto;
    padding: 30px;
    background: #f9f9f9;
    border-radius: 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    font-family: 'Segoe UI', sans-serif;
}

/* Page title */
.artist-list h1 {
    text-align: center;
    font-size: 2.5rem;
    color: #1e1e2f;
    margin-bottom: 30px;
}

/* Artist list */
.artist-list ul {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 0;
    margin: 0 0 30px 0;
    list-style: none;
}

.artist-link {
    background-color: #e3e6f3;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    color: #2a2a3b;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
}

.artist-link:hover {
    background-color: #5561ff;
    color: white;
    transform: translateY(-2px);
}

/* Selected artist section */
.artist-list h2 {
    margin-top: 40px;
    font-size: 2rem;
    color: #333;
    border-bottom: 2px solid #ddd;
    padding-bottom: 8px;
}

/* Song item container */
.eventContent__audio {
    background: #ffffff;
    border: 1px solid #ececec;
    border-radius: 12px;
    padding: 20px;
    margin: 20px 0;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
    transition: transform 0.2s ease;
}

.eventContent__audio:hover {
    transform: scale(1.01);
}

/* Song details */
.eventContent__audio-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #1e1e2f;
    display: block;
    margin-bottom: 6px;
}

.eventContent__audio-author,
.eventContent__audio-dur {
    font-size: 0.95rem;
    color: #666;
    margin-right: 15px;
}

/* Play button */
button {
    display: inline-block;
    margin-top: 12px;
    padding: 8px 18px;
    font-size: 0.95rem;
    border: none;
    border-radius: 6px;
    background: #5561ff;
    color: #fff;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
}

button:hover {
    background: #3b44c3;
    transform: scale(1.05);
}

/* Audio element (hidden but required) */
audio {
    display: none;
}

/* Responsive tweaks */
@media (max-width: 600px) {
    .artist-list {
        padding: 20px;
    }

    .artist-link {
        width: 100%;
        text-align: center;
    }

    .eventContent__audio {
        padding: 15px;
    }

    button {
        width: 100%;
    }
}

</style>
