# GitHub Copilot Instructions

## Coding Standards
- Follow Laravel best practices (service classes, form requests, named routes, tests in Pest).
- Use Inertia + Vue 3 composition API.
- Use shadcn-vue components.
- Use TypeScript on frontend.
- Use Eloquent API Resources and Laravel Actions pattern where appropriate.
- Prefer `$this->postJson()` and `$this->getJson()` in tests.

## Architecture Rules
- Keep controllers thin.
- Move business logic to Services/Actions.
- Keep Vue components small and composable.
- Use Wayfinder for route generation.
- Use Spatie Query Builder for filtering endpoints.

## PR Expectations
- Include explanation, implementation details, tests, edge cases, migration notes.
- Ensure 90%+ test coverage for new changes.
- Follow semantic commit naming.

## Don’ts
- Don’t generate inline CSS.
- Don’t hardcode URLs.
- Don’t use global helpers in Vue — use composables.
- Don’t modify core framework files.

## Output Format
- Use clean code.
- Avoid over-engineering.
- Conform to PSR-12, Laravel Pint.
