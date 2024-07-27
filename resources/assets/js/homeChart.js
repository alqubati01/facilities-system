/**
 * Charts ChartsJS
 */
'use strict';

(function () {
  // Color Variables
  const purpleColor = '#836AF9',
    yellowColor = '#ffe800',
    cyanColor = '#28dac6',
    orangeColor = '#FF8132',
    orangeLightColor = '#ffcf5c',
    oceanBlueColor = '#299AFF',
    greyColor = '#4F5D70',
    greyLightColor = '#EDF1F4',
    blueColor = '#2B9AFF',
    blueLightColor = '#84D0FF';

  let cardColor, headingColor, labelColor, borderColor, legendColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    borderColor = config.colors_dark.borderColor;
  } else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    legendColor = config.colors.bodyColor;
    borderColor = config.colors.borderColor;
  }

  // Set height according to their data-height
  // --------------------------------------------------------------------
  const chartList = document.querySelectorAll('.chartjs');
  chartList.forEach(function (chartListItem) {
    chartListItem.height = chartListItem.dataset.height;
  });

  // Bar Chart
  // --------------------------------------------------------------------
  const facilities_by_management = document.getElementById('facilities_by_management');
  if (facilities_by_management) {
    const facilities_by_management_Var = new Chart(facilities_by_management, {
      type: 'bar',
      data: {
        labels: facilities_by_management_branch_name,
        datasets: [
          {
            data: facilities_by_management_branch_count,
            backgroundColor: config.colors.primary,
            borderColor: 'transparent',
            maxBarThickness: 30,
            borderRadius: {
              topRight: 5,
              topLeft: 5
            }
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: number_of_facilities_by_management,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: Math.round(number_of_facilities_by_management / 5),
              color: labelColor
            }
          }
        }
      }
    });
  }

  // Doughnut Chart
  // --------------------------------------------------------------------

  const facilities_by_management_currencies = document.getElementById('facilities_by_management_currencies');
  if (facilities_by_management_currencies) {
    const facilities_by_management_currencies_Var = new Chart(facilities_by_management_currencies, {
      type: 'doughnut',
      data: {
        labels: facilities_by_management_currency_name,
        datasets: [
          {
            data: facilities_by_management_currency_count,
            backgroundColor: [config.colors.primary, cyanColor, orangeLightColor],
            borderWidth: 0,
            pointStyle: 'rectRounded'
          }
        ]
      },
      options: {
        responsive: true,
        animation: {
          duration: 500
        },
        cutout: '68%',
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              label: function (context) {
                const label = context.labels || '',
                  value = context.parsed;
                const output = ' ' + label + ' : ' + value;
                return output;
              }
            },
            // Updated default tooltip UI
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          }
        }
      }
    });
  }

  // Bar Chart
  // --------------------------------------------------------------------
  const facilities_by_branches = document.getElementById('facilities_by_branches');
  if (facilities_by_branches) {
    const facilities_by_branches_Var = new Chart(facilities_by_branches, {
      type: 'bar',
      data: {
        labels: facilities_by_branches_branch_name,
        datasets: [
          {
            data: facilities_by_branches_branch_count,
            backgroundColor: config.colors.primary,
            borderColor: 'transparent',
            maxBarThickness: 30,
            borderRadius: {
              topRight: 5,
              topLeft: 5
            }
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
          duration: 500
        },
        plugins: {
          tooltip: {
            rtl: isRtl,
            backgroundColor: cardColor,
            titleColor: headingColor,
            bodyColor: legendColor,
            borderWidth: 1,
            borderColor: borderColor
          },
          legend: {
            display: false
          }
        },
        scales: {
          x: {
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              color: labelColor
            }
          },
          y: {
            min: 0,
            max: number_of_facilities_by_branches,
            grid: {
              color: borderColor,
              drawBorder: false,
              borderColor: borderColor
            },
            ticks: {
              stepSize: Math.round(number_of_facilities_by_branches / 5),
              color: labelColor
            }
          }
        }
      }
    });
  }
})();
