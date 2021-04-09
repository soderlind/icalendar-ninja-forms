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
				this.listenTo(iCalendarDateSettingChannel, "render:setting", this.renderDate);
				this.listenTo(iCalendarTimeStartSettingChannel, "render:setting", this.renderTime);
				this.listenTo(iCalendarTimeEndSettingChannel, "render:setting", this.renderTime);
			}

			renderDate(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "date";
				element.attributes["pattern"] = "\d{4}-\d{2}-\d{2}";

				Object.assign(element.style, this.dateStyle );
			}

			renderTime(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "time";
				element.attributes["pattern"] = "\d{2}:\d{2}";

				Object.assign(element.style, this.dateStyle );
			}

		};

		new iCalendarsSettings();
	},
);
