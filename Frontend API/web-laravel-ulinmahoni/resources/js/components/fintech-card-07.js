// Import Chart.js
import {
  Chart, LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatValue, hexToRGB } from '../utils';

Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const fintechCard07 = () => {
  const ctx = document.getElementById('fintech-card-07');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=20')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const dataset1 = result.data.splice(0, 20);
      const dataset2 = result.data;

      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: result.labels,
          datasets: [
            // Indigo line
            {
              label: 'Mosaic Portfolio',
              data: dataset1,
              borderColor: tailwindConfig().theme.colors.indigo[500],
              fill: true,
              backgroundColor: `rgba(${hexToRGB(tailwindConfig().theme.colors.blue[500])}, 0.08)`,
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.indigo[500],
              clip: 20,
            },
            // Gray line
            {
              label: 'Expected Return',
              data: dataset2,
              borderColor: tailwindConfig().theme.colors.slate[300],
              fill: false,
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.slate[300],
              clip: 20,
            },
          ],
        },
        options: {
          layout: {
            padding: {
              top: 12,
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
              },
              ticks: {
                maxTicksLimit: 7,
                callback: (value) => formatValue(value),
              },
            },
            x: {
              type: 'time',
              time: {
                parser: 'MM-DD-YYYY',
                unit: 'month',
                displayFormats: {
                  month: 'MMM YY',
                },
              },
              grid: {
                display: false,
                drawBorder: false,
              },
              ticks: {
                autoSkipPadding: 48,
                maxRotation: 0,
              },
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

export default fintechCard07;
