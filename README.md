# Vreasy PHP Programming Test

## Objective

Develop an application which allows to retrieve the sunrise/sunset time for cities of the United
States. Use the following API to get the sunset/sunrise time:
[https://sunrise-sunset.org/api](https://sunrise-sunset.org/api)

The source code should be published to GitHub. <ins>Link to the public repository should be provided</ins>

Compose a short deployment guide as well as a short user guide.

## General requirements

- The application should contain the necessary set of RESTful services. 
- DB connection-related parameters should be set as environment variables. 
- Where needed, implement the structured exception handling and logging. 
- Unit tests implementation is highly desirable.

## Use cases

The app should expose the following resources:

### City

Fields:

- Name
- Latitude
- Longitude

Use cases:

- Get the list of all cities.
- Get a city by ID.
- Add a new city with the following parameters: name, latitude, longitude.
- Update an existing city.
- Delete an existing city.
- Search cities by name and/or coordinates.

### SunriseSunset

Fields:

- Sunrise time.
- Sunset time.
- Date.

Use cases:

- Given a city, retrieve sunrise/sunset time. Optionally:
  - A date can be parameterized (default: current date).
  - A time zone can be provided to format the response accordingly (default: UTC).

## Tools libraries and usage

- Apache HTTP Server
- Any PHP framework of your choice (Zend, Laravel, Symphony...)
- Composer
- MySQL
- PHPUnit
- Git