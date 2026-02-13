import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import Dashboard from './components/Dashboard';
import Settings from './components/Settings';

const App = () => {
    const [activeTab, setActiveTab] = useState('dashboard');

    return (
        <div className="wad-dashboard-container">
            <header style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                <h1 style={{ margin: 0 }}>Privacy-First Analytics</h1>
                <nav>
                    <button
                        className={`button ${activeTab === 'dashboard' ? 'button-primary' : ''}`}
                        onClick={() => setActiveTab('dashboard')}
                        style={{ marginRight: '10px' }}
                    >
                        Dashboard
                    </button>
                    <button
                        className={`button ${activeTab === 'settings' ? 'button-primary' : ''}`}
                        onClick={() => setActiveTab('settings')}
                    >
                        Settings
                    </button>
                </nav>
            </header>

            {activeTab === 'dashboard' && <Dashboard />}
            {activeTab === 'settings' && <Settings />}
        </div>
    );
};

export default App;
