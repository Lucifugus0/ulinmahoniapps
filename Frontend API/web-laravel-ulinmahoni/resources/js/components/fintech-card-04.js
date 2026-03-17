// Import Chart.js
import {
  Chart, BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend,
} from 'chart.js';

// Import utilities
import { tailwindConfig, formatValue } from '../utils';
// Import images
import revolutIcon from '../../../public/images/company-icon-01.svg';
import hsbcIcon from '../../../public/images/company-icon-02.svg';
import qontoIcon from '../../../public/images/company-icon-03.svg';
import n26Icon from '../../../public/images/company-icon-04.svg';


Chart.register(BarController, BarElement, LinearScale, CategoryScale, Tooltip, Legend);
const images = [revolutIcon, hsbcIcon, qontoIcon, n26Icon];

// A chart built with Chart.js 3
// https://www.chartjs.org/
const analyticsCard04 = () => {
  const ctx = document.getElementById('fintech-card-04');
  if (!ctx) return;

  fetch('/json-data-feed?datatype=19')
    .then(a => {
      return a.json();
    })
    .then(result => {

      const dataset1 = result.data.splice(0, 4);
      const dataset2 = result.data;

      const chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: result.labels,
          datasets: [
            // Indigo bars
            {
              label: 'Inflow',
              data: dataset1,
              backgroundColor: tailwindConfig().theme.colors.indigo[500],
              hoverBackgroundColor: tailwindConfig().theme.colors.indigo[600],
              categoryPercentage: 0.66,
            },
            // Gray bars
            {
              label: 'Outflow',
              data: dataset2,
              backgroundColor: tailwindConfig().theme.colors.slate[300],
              hoverBackgroundColor: tailwindConfig().theme.colors.slate[400],
              categoryPercentage: 0.66,
            },
          ],
        },
        options: {
          indexAxis: 'y',
          layout: {
            padding: {
              top: 12,
              bottom: 16,
              left: 72,
              right: 20,
            },
          },
          scales: {
            y: {
              grid: {
                display: false,
                drawBorder: false,
                drawTicks: false,
              },
              ticks: {
                display: false,
              },
            },
            x: {
              grid: {
                drawBorder: false,
              },
              ticks: {
                maxTicksLimit: 3,
                align: 'end',
                callback: (value) => formatValue(value),
              },
            },
          },
          plugins: {
            legend: {
              display: false,
            },
            htmlLegend: {
              // ID of the container to put the legend in
              containerID: 'fintech-card-04-legend',
            },
            tooltip: {
              callbacks: {
                title: () => false, // Disable tooltip title
                label: (context) => formatValue(context.parsed.x),
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
              li.style.marginRight = tailwindConfig().theme.margin[4];
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
          afterDraw(c) {
            const xAxis = c.scales.x;
            const yAxis = c.scales.y;
            yAxis.ticks.forEach((value, index) => {
              const y = yAxis.getPixelForTick(index);
              const image = new Image();
              image.src = images[index];
              c.ctx.drawImage(image, xAxis.left - 52, y - 18);
            });
          },
        }],
      });
    });
};

export default analyticsCard04;
