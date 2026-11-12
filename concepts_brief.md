# Dashboard Web de Scalping en Tiempo Real (100% Client-Side)

* **Author:** Marco Mondino
* **Date:** 2026-11-12

## Overview

Terminal web de análisis técnico institucional y scalping de alta frecuencia, ejecutada al 100% en el navegador (HTML5, CSS3, Vanilla JavaScript), sin dependencias de backend, sin necesidad de API keys privadas y con coste cero en tokens de IA. La solución se conecta a WebSockets públicos de baja latencia (Binance / Bybit) para transmitir datos tick a tick (<100 ms), visualiza un mapa de calor de profundidad de liquidez (Order Book Depth Heatmap en Canvas 2D), renderiza velas interactivas con marcado automático de zonas de oferta/demanda (Order Blocks), y genera un diagnóstico técnico procedimental en español mediante un motor algorítmico cuantitativo local.

## Analysis

### Síntesis Multidimensional (Goldfish Reviewers)
1. **Rendimiento Gráfico y Zero Garbage Collection (Codebase Details & Structural):**
   - El flujo continuo de ticks y deltas de profundidad a 100 ms puede saturar el recolector de basura del navegador y congelar el DOM.
   - **Solución implementada:** Uso de `TypedArrays` (`Float64Array`) y buffers circulares preasignados de tamaño fijo (300 a 500 columnas de tiempo) para evitar instanciación constante de objetos. El renderizado en Canvas se acopla estrictamente a `requestAnimationFrame()`.
2. **Normalización Visual y Navegabilidad del Heatmap (UX Design):**
   - Las órdenes masivas de ballenas pueden eclipsar órdenes intermedias si se usa una escala lineal.
   - **Solución implementada:** Mapeo de volumen con escala logarítmica y paleta cromática perceptiva (*The Kingfisher style*). Además, se incorporó navegación interactiva con **Zoom (0.6x a 3.5x)** mediante rueda del ratón o botones, **Pan (arrastre vertical)** para explorar niveles fuera de vista, y **Crosshair con Tooltip dinámico** para inspeccionar el precio y densidad de cada franja en tiempo real.
3. **Cold Start y Continuidad de Datos (Implementation Readiness):**
   - Dado que los WebSockets solo transmiten deltas, la apertura inicial de la página requiere un snapshot previo.
   - **Solución implementada:** Un `fetch` inicial a los endpoints REST públicos (`/api/v3/klines` y `/api/v3/depth`) para hidratar las primeras 100 velas y el libro de órdenes, conectando de inmediato el WebSocket para actualización continua sin saltos.
4. **Motor Cuantitativo Local vs. Latencia/Costo de LLM (Inventive Catalyst - TRIZ):**
   - Consumir LLMs introduce entre 1 y 3 segundos de latencia y facturación recurrente por tokens.
   - **Solución implementada:** Generador sintáctico determinista local en JavaScript. Evalúa en submilisegundos 5 vectores técnicos concurrentes (desbalance Bid/Ask en primeros 20 niveles, confluencia RSI en 1m/5m, posición de precio respecto a EMA 9/21, detección de muros de volumen y delta comprador/vendedor) y produce un párrafo explicativo en español idéntico al panel de la captura.
5. **Resiliencia de Conexión y Gestión de Memoria (Strategic Architecture):**
   - Integración con la `Page Visibility API`: al minimizar la pestaña, el stream pasa a modo buffer ligero para no consumir CPU innecesaria. Se implementa auto-reconexión exponencial y heartbeat (Ping/Pong) automático.

## Goals vs. Non-Goals

* **In-Scope Goals:**
  - Despliegue estático sin servidor (compatible con XAMPP / DonWeb / GitHub Pages / Cloudflare Pages).
  - Conexión nativa a WebSockets públicos de Binance / Bybit (sin credenciales ni claves privadas).
  - Selector dinámico de activos de alta liquidez (BTC/USDT, ETH/USDT, SOL/USDT, etc.).
  - Mapa de calor de profundidad del libro de órdenes (Order Book Depth Heatmap) en Canvas con escala logarítmica.
  - Gráfico interactivo con velas japonesas, medias móviles y detección automática de zonas de oferta y demanda.
  - Matriz multitemporal de indicadores (RSI en 1m, 5m, 15m, 1h, 1d y tendencias).
  - Widgets de sentimiento macro: Fear & Greed Index oficial de Alternative.me y cuenta regresiva de eventos macroeconómicos.
  - Generador procedural de diagnóstico de mercado en español, en tiempo real y sin costo de tokens.
  - Alerta sonora y visual de sobrecompra / sobreventa.

* **Out-of-Scope Non-Goals:**
  - Ejecución automatizada de órdenes de compra/venta con dinero real en cuentas de trading.
  - Backend pesado con base de datos SQL o almacenamiento persistente de ticks históricos de meses anteriores.
  - Consumo de APIs comerciales de pago (TradingView Premium, Kingfisher API, Coinglass API, OpenAI API).

## Alternatives Weighed

- **Alternativa 1 (Llamadas a LLM en Cloud vs. Motor Algorítmico Local):**
  - *Rechazada por el autor:* Costo de tokens inasumible y latencia de 1-3 segundos incompatible con scalping.
  - *Adoptada:* Motor cuantitativo procedural en JavaScript local (0 ms de latencia, coste 0).
- **Alternativa 2 (Backend Node/Python intermedio vs. Conexión Directa en Navegador):**
  - *Rechazada:* Requiere mantenimiento de servidor, proxies y eleva la complejidad de despliegue.
  - *Adoptada:* Arquitectura 100% client-side aprovechando los WebSockets públicos globales de Binance/Bybit.
- **Alternativa 3 (Heatmap de Liquidaciones Externo de Pago vs. Order Book Depth Heatmap Propio):**
  - *Rechazada:* APIs como Coinglass o Kingfisher exigen suscripciones costosas en dólares.
  - *Adoptada:* Renderizado propio en Canvas del libro de órdenes acumulado en memoria local, reflejando muros de liquidez reales.

## Key Decisions & Staging for Next Steps

- Estructura modular en Vanilla JS (`WSManager`, `OrderBookStore`, `HeatmapEngine`, `CandleEngine`, `AnalyticsEngine`, `MacroWidget`).
- Preparado para pasar a `/egm-draft` o implementación directa sobre `index.html`.
