import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';

const Settings = () => {
    const [storage, setStorage] = useState(window.wadSettings && window.wadSettings.currentStorageType ? window.wadSettings.currentStorageType : 'database');
    const [message, setMessage] = useState('');

    const handleSave = () => {
        setMessage('Saving...');
        apiFetch({
            path: '/wad/v1/settings',
            method: 'POST',
            data: { storage_type: storage }
        }).then(() => {
            setMessage('Settings Saved');
            if (window.wadSettings) {
                window.wadSettings.currentStorageType = storage;
            }
        }).catch((err) => {
            setMessage('Error: ' + err.message);
        });
    };

    return (
        <div className="wad-settings-panel">
            <h2 style={{ fontSize: '1.2em', marginBottom: '15px' }}>Data Storage Configuration</h2>
            <div style={{ marginBottom: '20px' }}>
                <p>Select where you want to store analytics data. Database is recommended for performance, while Text Files are useful for data portability.</p>
                <div style={{ padding: '10px 0' }}>
                    <label style={{ marginRight: '20px', display: 'inline-flex', alignItems: 'center', cursor: 'pointer' }}>
                        <input
                            type="radio"
                            name="storage_type"
                            value="database"
                            checked={storage === 'database'}
                            onChange={() => setStorage('database')}
                            style={{ marginRight: '8px' }}
                        />
                        Database (MySQL)
                    </label>
                    <label style={{ display: 'inline-flex', alignItems: 'center', cursor: 'pointer' }}>
                        <input
                            type="radio"
                            name="storage_type"
                            value="file"
                            checked={storage === 'file'}
                            onChange={() => setStorage('file')}
                            style={{ marginRight: '8px' }}
                        />
                        Text Files (JSON Logs)
                    </label>
                </div>
            </div>

            <button className="button button-primary" onClick={handleSave}>Save Changes</button>
            {message && <span style={{ marginLeft: '15px', fontWeight: 'bold', color: '#007cba' }}>{message}</span>}
        </div>
    );
};
export default Settings;
