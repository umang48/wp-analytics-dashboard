import { useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import { Line } from 'react-chartjs-2';

// Register components locally or rely on global registration
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
);

const Dashboard = () => {
    const [stats, setStats] = useState([]);
    const [period, setPeriod] = useState('7days');
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [totals, setTotals] = useState({ visitors: 0, pageviews: 0 });

    useEffect(() => {
        setLoading(true);
        apiFetch({ path: `/wad/v1/stats?period=${period}` })
            .then((data) => {
                setStats(data);

                // Calculate totals
                const tVisitors = data.reduce((acc, curr) => acc + (parseInt(curr.visitors) || 0), 0);
                const tPageviews = data.reduce((acc, curr) => acc + (parseInt(curr.pageviews) || 0), 0);
                setTotals({ visitors: tVisitors, pageviews: tPageviews });

                setLoading(false);
            })
            .catch((err) => {
                setError(err.message);
                setLoading(false);
            });
    }, [period]);

    if (loading) return <div className="wad-dashboard-container">Loading data...</div>;
    if (error) return <div className="wad-dashboard-container" style={{ color: 'red' }}>Error: {error}</div>;

    const labels = stats.map(item => item.date);
    const visitors = stats.map(item => item.visitors);
    const pageviews = stats.map(item => item.pageviews);

    const chartData = {
        labels,
        datasets: [
            {
                label: 'Unique Visitors',
                data: visitors,
                borderColor: 'rgb(53, 162, 235)',
                backgroundColor: 'rgba(53, 162, 235, 0.5)',
                tension: 0.3,
            },
            {
                label: 'Page Views',
                data: pageviews,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                tension: 0.3,
            },
        ],
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Traffic Overview' },
        },
        scales: {
            y: { beginAtZero: true }
        }
    };

    return (
        <div className="wad-dashboard-content">
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '20px' }}>
                <div style={{ display: 'flex', gap: '20px' }}>
                    <div className="wad-stat-card" style={{ minWidth: '150px' }}>
                        <div style={{ fontSize: '0.9em', color: '#666' }}>Visitors</div>
                        <div className="wad-stat-value">{totals.visitors}</div>
                    </div>
                    <div className="wad-stat-card" style={{ minWidth: '150px' }}>
                        <div style={{ fontSize: '0.9em', color: '#666' }}>Page Views</div>
                        <div className="wad-stat-value">{totals.pageviews}</div>
                    </div>
                </div>

                <select
                    value={period}
                    onChange={(e) => setPeriod(e.target.value)}
                    style={{ padding: '8px', borderRadius: '4px', border: '1px solid #aaa' }}
                >
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                </select>
            </div>

            <div className="wad-chart-container" style={{ height: '400px', position: 'relative' }}>
                <Line options={options} data={chartData} />
            </div>
        </div>
    );
};

export default Dashboard;
