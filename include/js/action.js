document.addEventListener(
	"DOMContentLoaded",
	() => {
		const nfRadio = Backbone.Radio;

		const iCalendarDateSettingChannel = nfRadio.channel(
			"setting-icalendar_date",
		);
		const iCalendarTimeStartSettingChannel = nfRadio.channel(
			"setting-icalendar_time_start",
		);
		const iCalendarTimeEndSettingChannel = nfRadio.channel(
			"setting-icalendar_time_end",
		);

		const iCalendarsSettings = class extends Marionette.Object {

			dateStyle = {
				'background': '#f9f9f9',
				'border': '0',
				'marginTop': '7px',
				'padding': '12px 15px',
				'width': '100%',
				'height': '41px',
			};
			/**
			 * initialize()
			 *
			 */
			initialize() {
				this.listenTo(iCalendarDateSettingChannel, "render:setting", this.renderDefaultDate);
				this.listenTo(iCalendarTimeStartSettingChannel, "render:setting", this.renderDefaultTime);
				this.listenTo(iCalendarTimeEndSettingChannel, "render:setting", this.renderDefaultTime);
			}

			renderDefaultDate(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "date";
				element.attributes["pattern"] = "\d{4}-\d{2}-\d{2}";
				if (!element.value) {
					element.valueAsDate = new Date()
				}
				Object.assign(element.style, this.dateStyle );
			}

			renderDefaultTime(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "time";
				element.attributes["pattern"] = "\d{2}:\d{2}";

				if (!element.value) {
					element.value = new Intl.DateTimeFormat('default', { // To specify options without a locale, use 'default' as a locale.
						hour: '2-digit',
						minute: '2-digit',
					}).format();
				}
				Object.assign(element.style, this.dateStyle );
			}

		};

		new iCalendarsSettings();
	},
);
