// Import Chart.js
import {
  Chart, LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip,
} from 'chart.js';
import 'chartjs-adapter-moment';

// Import utilities
import { tailwindConfig, formatValue, hexToRGB } from '../utils';

Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const fintechCard10 = () => {
  const ctx = document.getElementById('fintech-card-10');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=23')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: result.labels,
          datasets: [
            // Line
            {
              data: result.data,
              fill: true,
              backgroundColor: `rgba(${hexToRGB(tailwindConfig().theme.colors.rose[500])}, 0.08)`,
              borderColor: tailwindConfig().theme.colors.rose[500],
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.rose[500],
              clip: 20,
            },
          ],
        },
        options: {
          layout: {
            padding: {
              top: 16,
              bottom: 16,
              left: 20,
              right: 20,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false,
                drawTicks: false,
              },
              ticks: {
                maxTicksLimit: 2,
                display: false,
              },
            },
            x: {
              type: 'time',
              time: {
                parser: 'MM-DD-YYYY',
                unit: 'month',
              },
              display: false,
            },
          },
          plugins: {
            tooltip: {
              callbacks: {
                title: () => false, // Disable tooltip title
                label: (context) => formatValue(context.parsed.y),
              },
            },
            legend: {
              display: false,
            },
          },
          interaction: {
            intersect: false,
            mode: 'nearest',
          },
          maintainAspectRatio: false,
        },
      });
    });
};

export default fintechCard10;
