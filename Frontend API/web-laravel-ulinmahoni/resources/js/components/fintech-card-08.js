// Import Chart.js
import {
  Chart, LineController, LineElement, Filler, PointElement, LinearScale, CategoryScale, Tooltip,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatValue, hexToRGB } from '../utils';


/* eslint-disable max-len */
Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, CategoryScale, Tooltip);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const fintechCard08 = () => {
  const ctx = document.getElementById('fintech-card-08');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=21')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const dataset1 = result.data.splice(0, 2);
      const dataset2 = result.data;

      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: result.labels,
          datasets: [
            // Dark green line
            {
              label: 'Growth 1',
              data: dataset1,
              borderColor: tailwindConfig().theme.colors.emerald[500],
              fill: true,
              backgroundColor: `rgba(${hexToRGB(tailwindConfig().theme.colors.emerald[500])}, 0.08)`,
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.emerald[500],
              clip: 20,
            },
            // Light green line
            {
              label: 'Growth 2',
              data: dataset2,
              borderColor: tailwindConfig().theme.colors.emerald[200],
              fill: false,
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.emerald[200],
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
              grid: {
                display: false,
                drawBorder: false,
              },
              ticks: {
                autoSkipPadding: 48,
                maxRotation: 0,
                align: 'end',
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

export default fintechCard08;
