(function() {
    'use strict';

    // API Configuration - Using FreeDictionaryAPI as fallback
    const STORAGE_KEY = 'dictionary_history';
    const MAX_HISTORY = 10;

    // DOM Elements
    let toggleBtn, panel, closeBtn, searchInput, searchBtn, resultContainer, loadingContainer, historyList;

    // State
    let searchHistory = [];
    let currentSearchType = 'en-vi';

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        initializeElements();
        loadHistory();
        attachEventListeners();
        renderHistory();
    });

    function initializeElements() {
        toggleBtn = document.getElementById('dictionaryToggleBtn');
        panel = document.getElementById('dictionaryPanel');
        closeBtn = document.getElementById('dictionaryCloseBtn');
        searchInput = document.getElementById('dictionarySearchInput');
        searchBtn = document.getElementById('dictionarySearchBtn');
        resultContainer = document.getElementById('dictionaryResult');
        loadingContainer = document.getElementById('dictionaryLoading');
        historyList = document.getElementById('historyList');
    }

    function attachEventListeners() {
        // Toggle panel
        if (toggleBtn) {
            toggleBtn.addEventListener('click', togglePanel);
        }

        // Close panel
        if (closeBtn) {
            closeBtn.addEventListener('click', closePanel);
        }

        // Search
        if (searchBtn) {
            searchBtn.addEventListener('click', performSearch);
        }

        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    performSearch();
                }
            });
        }

        // Search type change
        const searchTypeRadios = document.querySelectorAll('input[name="searchType"]');
        searchTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                currentSearchType = this.value;
            });
        });

        // Click outside to close
        document.addEventListener('click', function(e) {
            if (panel && panel.classList.contains('active')) {
                if (!panel.contains(e.target) && !toggleBtn.contains(e.target)) {
                    closePanel();
                }
            }
        });
    }

    function togglePanel() {
        if (panel) {
            panel.classList.toggle('active');
            if (panel.classList.contains('active')) {
                searchInput.focus();
            }
        }
    }

    function closePanel() {
        if (panel) {
            panel.classList.remove('active');
        }
    }

    async function performSearch() {
        const keyword = searchInput.value.trim();
        
        if (!keyword) {
            showError('Vui lòng nhập từ cần tra!');
            return;
        }

        showLoading();

        try {
            const data = await searchWord(keyword, currentSearchType);
            
            if (data && data.status === 1) {
                displayResult(data.data);
                addToHistory(keyword);
            } else {
                showError('Không tìm thấy kết quả cho từ này.');
            }
        } catch (error) {
            console.error('Search error:', error);
            showError('Có lỗi xảy ra khi tra từ. Vui lòng thử lại!');
        } finally {
            hideLoading();
        }
    }

    async function searchWord(keyword, type) {
        try {
            if (type === 'en-vi') {
                // Use Free Dictionary API for English to Vietnamese
                return await searchEnglishWord(keyword);
            } else {
                // For Vietnamese to English, use Google Translate API alternative
                return await searchVietnameseWord(keyword);
            }
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    async function searchEnglishWord(keyword) {
        try {
            // Try Free Dictionary API first
            const url = `https://api.dictionaryapi.dev/api/v2/entries/en/${encodeURIComponent(keyword)}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data && Array.isArray(data) && data.length > 0) {
                return {
                    status: 1,
                    data: parseFreeDictionaryResponse(data[0])
                };
            }
            
            return { status: 0 };
        } catch (error) {
            console.error('English search error:', error);
            // Fallback to basic translation
            return {
                status: 1,
                data: {
                    word: keyword,
                    pronunciation: '',
                    type: '',
                    meanings: [{
                        definition: 'Không tìm thấy nghĩa chính xác. Vui lòng thử từ khác.',
                        example: ''
                    }],
                    audio: ''
                }
            };
        }
    }

    async function searchVietnameseWord(keyword) {
        // For Vietnamese to English, provide basic functionality
        // You can integrate with your backend API here
        return {
            status: 1,
            data: {
                word: keyword,
                pronunciation: '',
                type: 'Vietnamese',
                meanings: [{
                    definition: 'Chức năng tra Việt-Anh đang được phát triển. Vui lòng sử dụng tra Anh-Việt.',
                    example: ''
                }],
                audio: ''
            }
        };
    }

    function parseFreeDictionaryResponse(data) {
        const result = {
            word: data.word || '',
            pronunciation: '',
            type: '',
            meanings: [],
            audio: ''
        };

        try {
            // Get phonetic
            if (data.phonetic) {
                result.pronunciation = data.phonetic;
            } else if (data.phonetics && data.phonetics.length > 0) {
                result.pronunciation = data.phonetics[0].text || '';
                // Get audio
                if (data.phonetics[0].audio) {
                    result.audio = data.phonetics[0].audio;
                } else {
                    // Try to find any phonetic with audio
                    const phoneticWithAudio = data.phonetics.find(p => p.audio);
                    if (phoneticWithAudio) {
                        result.audio = phoneticWithAudio.audio;
                    }
                }
            }

            // Get meanings
            if (data.meanings && Array.isArray(data.meanings)) {
                data.meanings.forEach(meaning => {
                    const partOfSpeech = meaning.partOfSpeech || '';
                    
                    if (!result.type && partOfSpeech) {
                        result.type = partOfSpeech;
                    }

                    if (meaning.definitions && Array.isArray(meaning.definitions)) {
                        meaning.definitions.forEach(def => {
                            result.meanings.push({
                                definition: def.definition || '',
                                example: def.example || '',
                                type: partOfSpeech
                            });
                        });
                    }
                });
            }

            // If no meanings found
            if (result.meanings.length === 0) {
                result.meanings.push({
                    definition: 'Không tìm thấy nghĩa cho từ này.',
                    example: ''
                });
            }

        } catch (error) {
            console.error('Error parsing response:', error);
        }

        return result;
    }

    function displayResult(data) {
        if (!data || !resultContainer) return;

        const word = data.word || '';
        const pronunciation = data.pronunciation || '';
        const meanings = data.meanings || [];

        let html = `
            <div class="word-result">
                <div class="word-header">
                    <div class="word-text">${escapeHtml(word)}</div>
        `;

        if (pronunciation) {
            html += `
                <div class="word-pronunciation">
                    <span class="pronunciation-text">${escapeHtml(pronunciation)}</span>
                    ${data.audio ? `<button class="pronunciation-audio" onclick="window.dictionaryPlayAudio('${escapeHtml(data.audio)}')">
                        <i class="fas fa-volume-up"></i> Phát âm
                    </button>` : ''}
                </div>
            `;
        }

        if (data.type) {
            html += `<span class="word-type">${escapeHtml(data.type)}</span>`;
        }

        html += `</div><div class="word-meanings">`;

        if (meanings.length > 0) {
            meanings.forEach((meaning, index) => {
                const definition = meaning.definition || meaning.meaning || meaning;
                html += `
                    <div class="meaning-item">
                        ${meaning.type ? `<span class="meaning-type">${escapeHtml(meaning.type)}</span>` : ''}
                        <div class="meaning-definition">
                            ${meanings.length > 1 ? `${index + 1}. ` : ''}${escapeHtml(definition)}
                        </div>
                        ${meaning.example ? `
                            <div class="meaning-example">
                                <i class="fas fa-quote-left"></i> ${escapeHtml(meaning.example)}
                            </div>
                        ` : ''}
                    </div>
                `;
            });
        } else {
            html += '<div class="meaning-item"><div class="meaning-definition">Không có nghĩa được tìm thấy.</div></div>';
        }

        html += `</div></div>`;

        resultContainer.innerHTML = html;
    }

    function showLoading() {
        if (loadingContainer) loadingContainer.style.display = 'block';
        if (resultContainer) resultContainer.style.display = 'none';
    }

    function hideLoading() {
        if (loadingContainer) loadingContainer.style.display = 'none';
        if (resultContainer) resultContainer.style.display = 'block';
    }

    function showError(message) {
        if (!resultContainer) return;
        
        resultContainer.innerHTML = `
            <div class="dictionary-error">
                <i class="fas fa-exclamation-triangle"></i>
                <p>${escapeHtml(message)}</p>
                <small style="color: #999; font-size: 12px; margin-top: 8px; display: block;">
                    Gợi ý: Kiểm tra chính tả hoặc thử từ khác
                </small>
            </div>
        `;
    }

    function addToHistory(word) {
        if (!word) return;

        // Remove if already exists
        searchHistory = searchHistory.filter(item => item !== word);
        
        // Add to beginning
        searchHistory.unshift(word);
        
        // Limit history
        if (searchHistory.length > MAX_HISTORY) {
            searchHistory = searchHistory.slice(0, MAX_HISTORY);
        }

        saveHistory();
        renderHistory();
    }

    function loadHistory() {
        try {
            const stored = localStorage.getItem(STORAGE_KEY);
            if (stored) {
                searchHistory = JSON.parse(stored);
            }
        } catch (error) {
            console.error('Error loading history:', error);
            searchHistory = [];
        }
    }

    function saveHistory() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(searchHistory));
        } catch (error) {
            console.error('Error saving history:', error);
        }
    }

    function renderHistory() {
        if (!historyList) return;

        if (searchHistory.length === 0) {
            historyList.innerHTML = '<p style="color: #999; font-size: 13px; margin: 0;">Chưa có lịch sử tra cứu</p>';
            return;
        }

        const html = searchHistory.map(word => {
            const escapedWord = escapeHtml(word);
            const encodedWord = escapedWord.replace(/'/g, '&#39;');
            return `<span class="history-item" onclick="window.dictionarySearchFromHistory('${encodedWord}')">${escapedWord}</span>`;
        }).join('');

        historyList.innerHTML = html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Global functions - use window namespace to avoid conflicts
    window.dictionaryPlayAudio = function(audioUrl) {
        if (audioUrl) {
            // Handle both relative and absolute URLs
            const fullUrl = audioUrl.startsWith('http') ? audioUrl : `https://dict.laban.vn${audioUrl}`;
            const audio = new Audio(fullUrl);
            audio.play().catch(error => {
                console.error('Error playing audio:', error);
            });
        }
    };

    window.dictionarySearchFromHistory = function(word) {
        // Decode HTML entities
        const textarea = document.createElement('textarea');
        textarea.innerHTML = word;
        const decodedWord = textarea.value;
        
        if (searchInput) {
            searchInput.value = decodedWord;
            performSearch();
        }
    };

})();