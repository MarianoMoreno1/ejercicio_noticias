# Cómo correr cada versión

## 1. php-only

PHP renderiza el HTML. Vanilla JS maneja el carrusel.

```powershell
php -S localhost:8001 -t C:\Users\javie\PhpstormProjects\ejercicio_noticias\javier\php-only
```

Abrir: http://localhost:8001

Parar: Ctrl+C en la terminal.

---

## 2. php-react

PHP sirve el HTML y la API JSON. React (CDN) renderiza el carrusel.

```powershell
php -S localhost:8002 -t C:\Users\javie\PhpstormProjects\ejercicio_noticias\javier\php-react
```

Abrir: http://localhost:8002

Parar: Ctrl+C en la terminal.

Requiere internet (React y Babel vienen de unpkg.com).

---

## 3. react-component

Vite + React real. Build con npm.

```powershell
cd C:\Users\javie\PhpstormProjects\ejercicio_noticias\javier\react-component
npm run dev
```

Abrir: http://localhost:5173

Parar: Ctrl+C en la terminal.

Primera vez que clones el proyecto o borres node_modules, correr antes:

```powershell
npm install
```

---

## Correr los 3 a la vez

### Opción A — script automático (recomendado)

**PowerShell:**
```powershell
.\javier\start-all.ps1
```

**Git Bash:**
```bash
bash javier/start-all.sh
```

Inicia los 3 proyectos en background. Logs quedan en `javier/logs/`.
Parar: Enter (PowerShell) o Ctrl+C (Git Bash).

### Opción B — manual

Abrir 3 terminales separadas y correr uno en cada una.
No se pisan porque usan puertos distintos (8001, 8002, 5173).
