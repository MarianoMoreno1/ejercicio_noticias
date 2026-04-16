# ¿Por qué php-only tiene carousel.js y php-react no?

## php-only — JS separado

PHP solo genera el HTML estático y lo manda al browser. El browser recibe
el HTML ya armado con todos los `<article>` dentro del track, pero no sabe
cómo moverlos. Necesitás JS externo que tome ese DOM ya existente y le
agregue comportamiento (medir ancho, aplicar `translateX`, escuchar clicks).

Por eso existe `carousel.js`: es código que corre *después* de que PHP
terminó su trabajo.

```
PHP renderiza HTML → browser carga DOM → carousel.js manipula ese DOM
```

## php-react — JS inline en index.php

React no manipula un DOM que ya existe. React *construye* el DOM él mismo
desde cero usando su virtual DOM. Los componentes (`NewsCard`, `NewsCarousel`)
son funciones que describen qué renderizar y cómo reaccionar a eventos.

Todo eso vive dentro del `<script type="text/babel">` en `index.php` porque:

1. No hay build step — Babel lo transpila directo en el browser
2. El JS y el HTML shell son una sola unidad: `index.php` es la página,
   los componentes React son el contenido
3. Sacar los componentes a un `.js` separado funcionaría igual, pero
   requeriría un `fetch` o `import` extra sin ningún beneficio real aquí

```
PHP manda HTML shell vacío → React carga → React construye y maneja todo el DOM
```

## Resumen

| | php-only | php-react |
|---|---|---|
| ¿Quién construye el DOM? | PHP (servidor) | React (browser) |
| ¿Para qué sirve el JS? | Manipular DOM ya existente | Construir y controlar el DOM |
| ¿Dónde vive el JS? | `carousel.js` separado | Inline en `index.php` |
| ¿Por qué esa decisión? | PHP no puede agregar interactividad | React necesita un punto de entrada, no un DOM previo |
