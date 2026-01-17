{{-- Environment Debug Component - Only shown in non-production environments --}}
@if(config('app.env') !== 'production')
<div style="position: fixed; bottom: 10px; right: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 16px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); font-family: monospace; font-size: 12px; z-index: 9999; max-width: 350px;">
    <div style="font-weight: bold; margin-bottom: 8px; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 6px;">
        🔧 Environment Debug
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
@endif
