// src/axios.js
import axios from 'axios'

const instance = axios.create({
    baseURL: 'http://127.0.0.1:8000/api', // your Laravel backend
    headers: {
        'Content-Type': 'application/json',
    },
})

export default instance
