// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatThousands } from '../utils';

Chart.register(BarController, BarElement, LinearScale, TimeScale, Tooltip, Legend);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const analyticsCard03 = () => {
  const ctx = document.getElementById('analytics-card-03');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=12')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const dataset1 = result.data.splice(0, 6);
      const dataset2 = result.data.splice(0, 6);
      const dataset3 = result.data.splice(0, 6);
      const dataset4 = result.data;

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: result.labels,
          datasets: [
            // Stack
            {
              label: 'Direct',
              data: dataset1,
              backgroundColor: tailwindConfig().theme.colors.indigo[700],
              hoverBackgroundColor: tailwindConfig().theme.colors.indigo[800],
              barPercentage: 0.66,
              categoryPercentage: 0.66,
            },
            // Stack
            {
              label: 'Referral',
              data: dataset2,
              backgroundColor: tailwindConfig().theme.colors.indigo[500],
              hoverBackgroundColor: tailwindConfig().theme.colors.indigo[600],
              barPercentage: 0.66,
              categoryPercentage: 0.66,
            },
            // Stack
            {
              label: 'Organic Search',
              data: dataset3,
              backgroundColor: tailwindConfig().theme.colors.indigo[300],
              hoverBackgroundColor: tailwindConfig().theme.colors.indigo[400],
              barPercentage: 0.66,
              categoryPercentage: 0.66,
            },
            // Stack
            {
              label: 'Social',
              data: dataset4,
              backgroundColor: tailwindConfig().theme.colors.indigo[100],
              hoverBackgroundColor: tailwindConfig().theme.colors.indigo[200],
              barPercentage: 0.66,
              categoryPercentage: 0.66,
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
              stacked: true,
              grid: {
                drawBorder: false,
              },
              beginAtZero: true,
              ticks: {
                maxTicksLimit: 5,
                callback: (value) => formatThousands(value),
              },
            },
            x: {
              stacked: true,
              type: 'time',
              time: {
                parser: 'MM-DD-YYYY',
                unit: 'month',
                displayFormats: {
                  month: 'MMM',
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
            htmlLegend: {
              // ID of the container to put the legend in
              containerID: 'analytics-card-03-legend',
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
          animation: {
            duration: 200,
          },
          maintainAspectRatio: false,
        },
        plugins: [{
          id: 'htmlLegend',
          afterUpdate(c, args, options) {
            const legendContainer = document.getElementById(options.containerID);
            const ul = legendContainer.querySelector('ul');
            if (!ul) return;
            // Remove old legend items
            while (ul.firstChild) {
              ul.firstChild.remove();
            }
            // Reuse the built-in legendItems generator
            const items = c.options.plugins.legend.labels.generateLabels(c);
            items.forEach((item) => {
              const li = document.createElement('li');
              li.style.marginRight = tailwindConfig().theme.margin[3];
              // Button element
              const button = document.createElement('button');
              button.style.display = 'inline-flex';
              button.style.alignItems = 'center';
              button.style.opacity = item.hidden ? '.3' : '';
              button.onclick = () => {
                c.setDatasetVisibility(item.datasetIndex, !c.isDatasetVisible(item.datasetIndex));
                c.update();
              };
              // Color box
              const box = document.createElement('span');
              box.style.display = 'block';
              box.style.width = tailwindConfig().theme.width[3];
              box.style.height = tailwindConfig().theme.height[3];
              box.style.borderRadius = tailwindConfig().theme.borderRadius.full;
              box.style.marginRight = tailwindConfig().theme.margin[2];
              box.style.borderWidth = '3px';
              box.style.borderColor = item.fillStyle;
              box.style.pointerEvents = 'none';
              // Label
              const label = document.createElement('span');
              label.style.color = tailwindConfig().theme.colors.slate[500];
              label.style.fontSize = tailwindConfig().theme.fontSize.sm[0];
              label.style.lineHeight = tailwindConfig().theme.fontSize.sm[1].lineHeight;
              const labelText = document.createTextNode(item.text);
              label.appendChild(labelText);
              li.appendChild(button);
              button.appendChild(box);
              button.appendChild(label);
              ul.appendChild(li);
            });
          },
        }],
      });
    });
};

export default analyticsCard03;
