import { useState, useEffect } from 'react';
import { NewsCarousel } from './components/NewsCarousel';
import './App.css';

export default function App() {
    // useState guarda datos que pueden cambiar y disparan un re-render al hacerlo
    const [news, setNews]       = useState([]);   // array de noticias (vacío al inicio)
    const [loading, setLoading] = useState(true); // true mientras esperamos la API
    const [error, setError]     = useState(null); // null si todo fue bien

    // useEffect con [] vacío = se ejecuta UNA sola vez cuando el componente aparece en pantalla
    // Es el lugar correcto para hacer fetch: no en el render, para no repetirlo en cada re-render
    useEffect(() => {
        // En dev, Vite intercepta /api.php y lo redirige a localhost:8003 (vite.config.js → proxy)
        // En producción, /api.php estaría en el mismo servidor que sirve React
        fetch('/api.php')
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                return res.json(); // convierte el body JSON a array JS
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

    // Renderizado condicional: devolvemos distinto JSX según el estado actual
    if (loading) return <p style={{ textAlign: 'center', padding: '2rem' }}>Cargando noticias…</p>;
    if (error)   return <p style={{ textAlign: 'center', padding: '2rem', color: 'red' }}>Error: {error}</p>;

    return (
        <main>
            <NewsCarousel news={news} title="Últimas Noticias" />
        </main>
    );
}
