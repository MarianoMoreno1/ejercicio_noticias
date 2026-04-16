import { NewsCarousel } from './components/NewsCarousel';
import './App.css';

// Datos mock — reemplazar con fetch('/api.php') si se conecta al backend PHP
const NEWS = [
    { id: 1, title: 'Nuevo avance en energía solar',       summary: 'Investigadores logran un panel solar con 47% de eficiencia, el más alto registrado.',      image: 'https://picsum.photos/seed/1/400/250' },
    { id: 2, title: 'Inteligencia artificial en medicina', summary: 'Un modelo de IA detecta cáncer de pulmón en etapa temprana con precisión del 94%.',        image: 'https://picsum.photos/seed/2/400/250' },
    { id: 3, title: 'Exploración de Marte 2026',           summary: 'La NASA confirma fecha de lanzamiento para la misión tripulada a Marte.',                   image: 'https://picsum.photos/seed/3/400/250' },
    { id: 4, title: 'Récord mundial en ajedrez',           summary: 'Magnus Carlsen supera su récord de Elo con una racha de 127 partidas sin perder.',          image: 'https://picsum.photos/seed/4/400/250' },
    { id: 5, title: 'Crisis hídrica en el sur',            summary: 'Tres provincias declaran emergencia por sequía prolongada.',                                 image: 'https://picsum.photos/seed/5/400/250' },
    { id: 6, title: 'Startup local recauda 50M',           summary: 'Empresa de logística cierra ronda Serie B con inversores europeos.',                        image: 'https://picsum.photos/seed/6/400/250' },
    { id: 7, title: 'Festival de cine en Rosario',         summary: 'La décima edición del festival reúne más de 200 películas de 40 países.',                   image: 'https://picsum.photos/seed/7/400/250' },
    { id: 8, title: 'Nueva ley de teletrabajo',            summary: 'El congreso aprueba regulación que obliga a cubrir gastos del hogar en teletrabajo.',       image: 'https://picsum.photos/seed/8/400/250' },
    { id: 9, title: 'Descubrimiento arqueológico',         summary: 'Hallaron una ciudad inca subacuática en el lago Titicaca con más de 500 años.',             image: 'https://picsum.photos/seed/9/400/250' },
];

export default function App() {
    return (
        <main>
            <NewsCarousel news={NEWS} title="Últimas Noticias" />
        </main>
    );
}
