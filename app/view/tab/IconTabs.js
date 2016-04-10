/**
 * Created by Administrator on 2016/2/26.
 */
Ext.define('svgxml.view.tab.IconTabs', {
    extend: 'Ext.container.Container',
    xtype: 'icon-tabs',
    width: 400,


    defaults: {
        xtype: 'tabpanel',
        width: 400,
        height: 200,
        defaults: {
            bodyPadding: 10,
            autoScroll: true
        }
    },

    items: [/*{
        margin: '0 0 20 0',
        items: [{
            glyph: 72,
            //html: KitchenSink.DummyText.longText
        }, {
            glyph: 99,
            //html: KitchenSink.DummyText.extraLongText
        }, {
            glyph: 42,
            disabled: true
        }]
    }, */{
        plain: true,
        items: [{
            title: 'Active Tab',
            glyph: 72
            //html: KitchenSink.DummyText.longText
        }, {
            title: 'Inactive Tab',
            glyph: 99
            //html: KitchenSink.DummyText.extraLongText
        }, {
            title: 'Disabled Tab',
            glyph: 42,
            disabled: true
        }]
    }]
});