import { jsOMS }      from '../../jsOMS/Utils/oLib.js';
import { Autoloader } from '../../jsOMS/Autoloader.js';

Autoloader.defineNamespace('omsApp.Modules');

/* global omsApp */
omsApp.Modules.ItemManagement = class {
    /**
     * @constructor
     *
     * @since 1.0.0
     */
    constructor (app)
    {
        this.app = app;
    };

    bind (id)
    {
        const e = typeof id === 'undefined'
            ? document.getElementsByTagName('canvas')
            : [document.getElementById(id)];

        const length = e.length;

        for (let i = 0; i < length; ++i) {
            if (e[i].getAttribute('data-chart') === null
                && e[i].getAttribute('data-chart') !== 'undefined'
            ) {
                continue;
            }

            this.bindElement(e[i]);
        }
    };

    bindElement (chart)
    {
        if (typeof chart === 'undefined' || !chart) {
            jsOMS.Log.Logger.instance.error('Invalid chart: ' + chart, 'ItemManagementController');

            return;
        }

        const data = JSON.parse(chart.getAttribute('data-chart'));

        /* global Chart */
        // eslint-disable-next-line no-unused-vars
        const myChart = new Chart(chart.getContext('2d'), data);
    };
};

window.omsApp.moduleManager.get('ItemManagement').bind();
