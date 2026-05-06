  </main>
  <footer class="footer">
    &copy; <?= date('Y') ?> <strong style="color:var(--primary)">Address Book</strong> — Built by Laiba Misbah and Alishbah DISM eProject.
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
// Dynamic Search Functionality
const searchInput = document.getElementById('search_input');
const searchResults = document.getElementById('search_results');
let searchTimeout;

if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch('search_handler.php?q=' + encodeURIComponent(query))
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.results.length > 0) {
                        let html = '<div style="padding:8px;">';
                        
                        data.results.forEach(item => {
                            let resultHtml = '';
                            
                            if (item.type === 'product') {
                                resultHtml = `
                                    <a href="${item.page}" style="display:block; padding:10px; border-bottom:1px solid #eee; color:#000; text-decoration:none; transition:0.2s;">
                                        <div style="font-weight:600; font-size:13px;">${item.name}</div>
                                        <div style="font-size:11px; color:#999; margin-top:2px;">${item.category} • $${item.price}</div>
                                    </a>
                                `;
                            } else if (item.type === 'user') {
                                resultHtml = `
                                    <a href="${item.page}" style="display:block; padding:10px; border-bottom:1px solid #eee; color:#000; text-decoration:none; transition:0.2s;">
                                        <div style="font-weight:600; font-size:13px;">👤 ${item.name}</div>
                                        <div style="font-size:11px; color:#999; margin-top:2px;">${item.email} • ${item.phone}</div>
                                    </a>
                                `;
                            } else if (item.type === 'order') {
                                resultHtml = `
                                    <a href="${item.page}" style="display:block; padding:10px; border-bottom:1px solid #eee; color:#000; text-decoration:none; transition:0.2s;">
                                        <div style="font-weight:600; font-size:13px;">📦 Order #${item.id}</div>
                                        <div style="font-size:11px; color:#999; margin-top:2px;">${item.name} • $${item.total}</div>
                                    </a>
                                `;
                            } else if (item.type === 'main_category' || item.type === 'sub_category') {
                                resultHtml = `
                                    <a href="${item.page}" style="display:block; padding:10px; border-bottom:1px solid #eee; color:#000; text-decoration:none; transition:0.2s;">
                                        <div style="font-weight:600; font-size:13px;">📂 ${item.name}</div>
                                    </a>
                                `;
                            } else if (item.type === 'shade') {
                                resultHtml = `
                                    <a href="${item.page}" style="display:block; padding:10px; border-bottom:1px solid #eee; color:#000; text-decoration:none; transition:0.2s;">
                                        <div style="font-weight:600; font-size:13px;">🎨 ${item.name}</div>
                                        <div style="font-size:11px; color:#999; margin-top:2px;">${item.code}</div>
                                    </a>
                                `;
                            }
                            
                            html += resultHtml;
                        });
                        
                        html += '</div>';
                        searchResults.innerHTML = html;
                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div style="padding:12px; text-align:center; color:#999; font-size:13px;">کوئی نتیجہ نہیں ملے</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(err => console.error('Search error:', err));
        }, 300);
    });
    
    // Close search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}
</script>
</body>
</html>
