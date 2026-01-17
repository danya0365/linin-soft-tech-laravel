{{-- Environment Debug Component - Only shown in non-production environments --}}
@if(config('app.env') !== 'production')
<div id="env-debug-panel" style="position: fixed; bottom: 10px; right: 10px; z-index: 9999;">
    <!-- Minimized Button -->
    <button id="env-debug-toggle" onclick="toggleEnvDebug()" style="display: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; padding: 10px 14px; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.3); font-size: 18px; width: 44px; height: 44px; transition: transform 0.2s;">
        🔧
    </button>
    
    <!-- Full Debug Panel -->
    <div id="env-debug-content" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 16px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); font-family: monospace; font-size: 12px; max-width: 350px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 6px;">
            <div style="font-weight: bold; font-size: 14px;">
                🔧 Environment Debug
            </div>
            <button onclick="toggleEnvDebug()" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 16px; line-height: 1;">
                ×
            </button>
        </div>
        <div style="display: grid; gap: 4px;">
            <div>
                <span style="opacity: 0.8;">ENV:</span> 
                <strong style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px;">{{ config('app.env') }}</strong>
            </div>
            <div>
                <span style="opacity: 0.8;">URL:</span> 
                <strong style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px; font-size: 11px;">{{ config('app.url') }}</strong>
            </div>
            <div>
                <span style="opacity: 0.8;">DB:</span> 
                <strong style="background: rgba(255,255,255,0.2); padding: 2px 6px; border-radius: 3px;">{{ config('database.connections.mysql.database') }}</strong>
            </div>
            <div style="margin-top: 4px; padding-top: 6px; border-top: 1px solid rgba(255,255,255,0.3);">
                <span style="opacity: 0.8;">DEBUG:</span> 
                <strong style="background: {{ config('app.debug') ? '#10b981' : '#ef4444' }}; padding: 2px 6px; border-radius: 3px;">{{ config('app.debug') ? 'ON' : 'OFF' }}</strong>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleEnvDebug() {
        const content = document.getElementById('env-debug-content');
        const toggle = document.getElementById('env-debug-toggle');
        
        if (content.style.display === 'none') {
            content.style.display = 'block';
            toggle.style.display = 'none';
            localStorage.setItem('envDebugVisible', 'true');
        } else {
            content.style.display = 'none';
            toggle.style.display = 'block';
            localStorage.setItem('envDebugVisible', 'false');
        }
    }

    // Restore state from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const isVisible = localStorage.getItem('envDebugVisible');
        if (isVisible === 'false') {
            toggleEnvDebug();
        }
    });
</script>
@endif
