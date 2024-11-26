# ADR 002 - API first

Date: 26-11-2024

## Status

Accepted

## Context

The "API first" approach is to enforce that all interactions with the system must go through the API.
See more about the "API first" approach [here](https://swagger.io/resources/articles/adopting-an-api-first-approach/).

The previous version of OS2Display was used without the admin module in some contexts.
We want to support other uses than the standard OS2Display setup.

By adopting the API first approach it will be possible to replace clients without rewriting the entire application.
This will make the system more future-proof.

[OpenAPI](https://www.openapis.org/) is a standard for describing an API.

## Decision

We will use an API first approach where the only way to manage content is through calls to the API.
The API specification will be included [with the project](../../public/api-spec-v2.json) and kept up to date.

## Consequences

The main consequence is that all interactions with data in the system should be implemented in the API.
This can in some cases be more work, but will give the benefit that the interaction can be used in new contexts later
on.

By supplying an OpenAPI specification clients will be able to auto-generate code for interacting with the API.
This will make it easier to write clients for the system.
