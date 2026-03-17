// Import Chart.js
import {
  Chart, PolarAreaController, RadialLinearScale, Tooltip, Legend,
} from 'chart.js';
import 'chartjs-adapter-moment';

// Import utilities
import { tailwindConfig, hexToRGB } from '../utils';

Chart.register(PolarAreaController, RadialLinearScale, Tooltip, Legend);

// A chart built with Chart.js 3
// https://www.chartjs.org/
const analyticsCard10 = () => {
  const ctx = document.getElementById('analytics-card-10');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=16')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const chart = new Chart(ctx, {
        type: 'polarArea',
        data: {
          labels: result.labels,
          datasets: [
            {
              label: 'Sessions By Gender',
              data: result.data,
              backgroundColor: [
                `rgba(${hexToRGB(tailwindConfig().theme.colors.indigo[500])}, 0.8)`,
                `rgba(${hexToRGB(tailwindConfig().theme.colors.sky[400])}, 0.8)`,
                `rgba(${hexToRGB(tailwindConfig().theme.colors.emerald[500])}, 0.8)`,
              ],
              hoverBackgroundColor: [
                `rgba(${hexToRGB(tailwindConfig().theme.colors.indigo[600])}, 0.8)`,
                `rgba(${hexToRGB(tailwindConfig().theme.colors.sky[500])}, 0.8)`,
                `rgba(${hexToRGB(tailwindConfig().theme.colors.emerald[600])}, 0.8)`,
              ],
              hoverBorderColor: tailwindConfig().theme.colors.white,
            },
          ],
        },
        options: {
          layout: {
            padding: 24,
          },
          plugins: {
            legend: {
              display: false,
            },
            htmlLegend: {
              // ID of the container to put the legend in
              containerID: 'analytics-card-10-legend',
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
              li.style.margin = tailwindConfig().theme.margin[1];
              // Button element
              const button = document.createElement('button');
              button.classList.add('btn-xs');
              button.style.backgroundColor = tailwindConfig().theme.colors.white;
              button.style.borderWidth = tailwindConfig().theme.borderWidth[1];
              button.style.borderColor = tailwindConfig().theme.colors.slate[200];
              button.style.color = tailwindConfig().theme.colors.slate[500];
              button.style.boxShadow = tailwindConfig().theme.boxShadow.md;
              button.style.opacity = item.hidden ? '.3' : '';
              button.onclick = () => {
                c.toggleDataVisibility(item.index, !item.index);
                c.update();
              };
              // Color box
              const box = document.createElement('span');
              box.style.display = 'block';
              box.style.width = tailwindConfig().theme.width[2];
              box.style.height = tailwindConfig().theme.height[2];
              box.style.backgroundColor = item.fillStyle;
              box.style.borderRadius = tailwindConfig().theme.borderRadius.sm;
              box.style.marginRight = tailwindConfig().theme.margin[1];
              box.style.pointerEvents = 'none';
              // Label
              const label = document.createElement('span');
              label.style.display = 'flex';
              label.style.alignItems = 'center';
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

export default analyticsCard10;
