/**
 * Add date and time picker to iCalendar action setting.
 *
 * @author Per Soderlind http://soderlind.no
 *
 */
document.addEventListener(
	"DOMContentLoaded",
	() => {
		const nfRadio = Backbone.Radio; // rome-ignore lint/js/noUndeclaredVariables: Backbone is an external object.

		const iCalendarDateSettingChannel = nfRadio.channel(
			"setting-icalendar_date",
		);
		const iCalendarTimeStartSettingChannel = nfRadio.channel(
			"setting-icalendar_time_start",
		);
		const iCalendarTimeEndSettingChannel = nfRadio.channel(
			"setting-icalendar_time_end",
		);

		const iCalendarsSettings = class extends Marionette.Object { // rome-ignore lint/js/noUndeclaredVariables: Marionett is an external object.
			nfTextboxStyle = {
				"background": "#f9f9f9",
				"border": "0",
				"marginTop": "7px",
				"padding": "12px 15px",
				"width": "100%",
				"height": "41px",
			};
			/**
			 * initialize()
			 *
			 */
			initialize() {
				this.listenTo(
					iCalendarDateSettingChannel,
					"render:setting",
					this.renderDateField,
				);
				this.listenTo(
					iCalendarTimeStartSettingChannel,
					"render:setting",
					this.renderTimeField,
				);
				this.listenTo(
					iCalendarTimeEndSettingChannel,
					"render:setting",
					this.renderTimeField,
				);
			}

			/**
			 * Convert the textbox (input type="text") to date field (input type="date").
			 *
			 * - If empty, set the date to "today", in the format YYYY-MM-DD.
			 * - Set the style of the field to the same as a textbox.
			 *
			 * @see https://caniuse.com/input-datetime
			 *
			 * @param {*} settingModel
			 * @param {*} dataModel
			 * @param {*} view
			 */
			renderDateField(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "date";
				element.attributes["pattern"] = "d{4}-d{2}-d{2}";
				if (!element.value) {
					element.valueAsDate = new Date();
				}
				Object.assign(element.style, this.nfTextboxStyle);
			}

			/**
			 * Convert the textbox (input type="text") to time field (input type="time").
			 *
			 * - If empty, set the time to "now", in the format HH:MM
			 * - Set the style of the field to the same as a textbox.
			 *
			 * @see https://caniuse.com/input-datetime
			 *
			 * @param {*} settingModel
			 * @param {*} dataModel
			 * @param {*} view
			 */
			renderTimeField(settingModel, dataModel, view) {
				const element = view.el.getElementsByClassName("setting")[0];
				element.attributes["type"].value = "time";
				element.attributes["pattern"] = "d{2}:d{2}";

				if (!element.value) {
					element.value = new Intl.DateTimeFormat(
						"default", // To specify options without a locale, use 'default' as a locale.
						{
							hour: "2-digit",
							minute: "2-digit",
						},
					).format();
				}
				Object.assign(element.style, this.nfTextboxStyle);
			}
		};

		new iCalendarsSettings();
	},
);
