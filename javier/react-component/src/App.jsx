import { useState, useEffect } from 'react';
import { NewsCarousel } from './components/NewsCarousel';
import './App.css';

export default function App() {
    const [news, setNews]       = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError]     = useState(null);

    useEffect(() => {
        fetch('/api.php')
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json();
            })
            .then(data => {
                setNews(data);
                setLoading(false);
            })
            .catch(err => {
                setError(err.message);
                setLoading(false);
            });
    }, []);

    if (loading) return <p style={{ textAlign: 'center', padding: '2rem' }}>Cargando noticias…</p>;
    if (error)   return <p style={{ textAlign: 'center', padding: '2rem', color: 'red' }}>Error: {error}</p>;

    return (
        <main>
            <NewsCarousel news={news} title="Últimas Noticias" />
        </main>
    );
}
