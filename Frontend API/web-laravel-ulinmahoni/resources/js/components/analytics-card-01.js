// Import Chart.js
import {
  Chart, LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatThousands, hexToRGB } from '../utils';

Chart.register(LineController, LineElement, Filler, PointElement, LinearScale, TimeScale, Tooltip);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const analyticsCard01 = () => {
  const ctx = document.getElementById('analytics-card-01');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=10')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const dataset1 = result.data.splice(0, 26);
      const dataset2 = result.data;

      const chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: result.labels,
          datasets: [
            // Indigo line
            {
              label: 'Current',
              data: dataset1,
              fill: true,
              backgroundColor: `rgba(${hexToRGB(tailwindConfig().theme.colors.blue[500])}, 0.08)`,
              borderColor: tailwindConfig().theme.colors.indigo[500],
              borderWidth: 2,
              tension: 0,
              pointRadius: 0,
              pointHoverRadius: 3,
              pointBackgroundColor: tailwindConfig().theme.colors.indigo[500],
              clip: 20,
            },
            // Gray line
            {
              label: 'Previous',
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
            padding: 20,
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false,
              },
              ticks: {
                callback: (value) => formatThousands(value),
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
            legend: {
              display: false,
            },
            tooltip: {
              callbacks: {
                title: () => false, // Disable tooltip title
                label: (context) => formatThousands(context.parsed.y),
              },
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

export default analyticsCard01;
