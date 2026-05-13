# Appointment widget

Built on native web components. No inline css, only external css from holding page will be used.

## API endpoints and VITE_API_BASE

All endpoints are given relative to VITE_API_BASE defined in .env file for Vite build.

## Rendering pipeline

before first render the component should fetch:
* doctor/ API endpoint
* schedule/ API endpoint
if any of that endpoints return non-200 http code, display error message, otherwise proceed to render.

##i18n

All UI messages should be hard-coded in Russian.

## Sub-components

###Services

Displayed immediately after successful API data fetch. A list of this specialist's services with the cost and duration of the appointment for each service (taken from the API response).
Displayed as clickable tags. Click on tag selects the service. Only one service can be selected.

###Calendar

Displayed if a service is selected, hidden otherwise. A 1-month calendar with dates. Can display next month and back.

* The display of days in the calendar depends on the specialist's schedule:
- Days when appointments are NOT available (the specialist is not working or is booked by other patients): opacity 50%, not clickable
- Days when appointments are available: opacity 100%, clickable
- Days when appointments are already booked with the specialist but appointments are still available: opacity 100%, yellow background, clickable

* Clicking on any day for which an appointment is available displays the schedule sub-component

###Schedule
Clickable tags, each representing possible appointment start time fetched from API.

* The display of schedule tags depends on existing appointments:
- Appointment slots for which appointments are NOT available (the specialist is unavailable or booked by other patients): opacity 50%, not clickable
- Appointment slots for which appointments are available: opacity 100%, clickable

* Clicking on any appointment slot for which an appointment is available:
- The slot is highlighted in green
- The "Book Now" button appears

* Clicking on the "Book Now" button displays the Form

###Form

Handled by a 3rd-party javascript library. Mock:

<form
	data-action="/api/medflex/appointment-specialist"
	data-validator="/api/validator/medflex/appointment-specialist"
	data-method="post"
>
	...
</form>