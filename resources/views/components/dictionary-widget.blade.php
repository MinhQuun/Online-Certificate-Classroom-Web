<div class="dictionary-widget">
    <!-- Floating Button -->
    <button class="dictionary-toggle-btn" id="dictionaryToggleBtn" title="Tra từ điển">
        <i class="fas fa-book"></i>
    </button>

    <!-- Dictionary Panel -->
    <div class="dictionary-panel" id="dictionaryPanel">
        <div class="dictionary-header">
            <h5 class="dictionary-title">
                <i class="fas fa-book-open me-2"></i>
                Tra từ vựng
            </h5>
            <button class="dictionary-close-btn" id="dictionaryCloseBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="dictionary-body">
            <!-- Search Form -->
            <div class="dictionary-search">
                <div class="search-input-group">
                    <input 
                        type="text" 
                        id="dictionarySearchInput" 
                        class="dictionary-search-input" 
                        placeholder="Nhập từ cần tra..."
                        autocomplete="off"
                    >
                    <button class="dictionary-search-btn" id="dictionarySearchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div class="dictionary-loading" id="dictionaryLoading" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
                <p class="mt-2">Đang tra từ...</p>
            </div>

            <!-- Result Container -->
            <div class="dictionary-result" id="dictionaryResult">
                <div class="dictionary-placeholder">
                    <i class="fas fa-book-reader"></i>
                    <p>Nhập từ cần tra và nhấn Enter hoặc nút tìm kiếm</p>
                </div>
            </div>

            <!-- Search History -->
            <div class="dictionary-history" id="dictionaryHistory">
                <h6 class="history-title">Lịch sử tra cứu</h6>
                <div class="history-list" id="historyList"></div>
            </div>
        </div>
    </div>
</div>