document.addEventListener(
	"DOMContentLoaded",
	() => {
		const nfRadio = Backbone.Radio;
		const simpleEventDateChannel = nfRadio.channel(
			"actionSetting-icalendar_date",
		);
		const simpleEventTimeStartChannel = nfRadio.channel(
			"actionSetting-icalendar_time_start",
		);
		const simpleEventTimeEndChannel = nfRadio.channel(
			"actionSetting-icalendar_time_end",
		);

		const SimpleEventsSettings = class extends Marionette.Object {
			/**
			 * initialize()
			 *
			 */
			initialize() {
				this.listenTo(simpleEventDateChannel, "update:setting", this.eventDate);
				this.listenTo(simpleEventTimeStartChannel, "update:setting", this.timeStart);
				this.listenTo(simpleEventTimeEndChannel, "update:setting", this.timeEnd);
			}

			eventDate(dataModel, settingModel) {
				if ("undefined" === typeof settingModel) {
					return;
				}
				const value = dataModel.get("icalendar_date").trim();

				if (value && !this.isValidDateFormat(value)) {
					return settingModel.set("warning", icalnfi18n.errorInvalidDateFormat);
				}

				return settingModel.set("warning", false);
			}

			timeStart(dataModel, settingModel) {
				if ("undefined" === typeof settingModel) {
					return;
				}

				const value = dataModel.get("icalendar_time_start").trim();

				if (value && !this.isValidTimeFormat(value)) {
					return settingModel.set("warning", icalnfi18n.errorInvalidTimeFormat);
				}

				return settingModel.set("warning", false);
			}

			timeEnd(dataModel, settingModel) {
				if ("undefined" === typeof settingModel) {
					return;
				}

				const value = dataModel.get("icalendar_time_end").trim();

				if (value && !this.isValidTimeFormat(value)) {
					return settingModel.set("warning", icalnfi18n.errorInvalidTimeFormat);
				}

				return settingModel.set("warning", false);
			}

			isValidDateFormat(eventDate) {
				return /^\d{4}-\d{2}-\d{2}$/.test(eventDate);
			}
			isValidTimeFormat(eventTime) {
				return /^\d{2}:\d{2}$/.test(eventTime);
			}
		};

		new SimpleEventsSettings();
	},
);
