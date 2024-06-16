panel.plugin('chuntfm/series-events', {
  sections: {
    'series-events': {
      data: function () {
        return {
          label: null,
          layout: 'list',
          events: []
        }
      },
      created: function() {
        this.load().then(response => {
          this.label = response.label;
          this.layout = response.layout;
          this.events  = response.events;
          //console.log(this.events);
        });
      },
      template: `
        <section class="k-section k-series-events-section">
            <header class="k-section-header">
              <h2 class="k-headline">{{ label }}</h2>
            </header>
            <k-collection
              :items="events"
              :layout="layout"
            />
        </section>
      `,
    }
  }
});
