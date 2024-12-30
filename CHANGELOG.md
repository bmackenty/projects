# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/), and this project adheres to [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2024-12-28
### Added
- Initial release of the project.
- User authentication with login and registration functionality.
- CRUD operations for managing tasks.
- Responsive design for desktop and mobile devices.
- File upload functionality (PDF and image files only).
- TinyMCE integration for rich text editing.
- Basic reporting features, including task summaries.

### Fixed
- N/A (Initial release).

### Changed
- N/A (Initial release).

## [1.0.1] - 2024-12-30

### Fixed
- Task assignments not writing to database due to missing transaction handling and validation
- Comments display issues in task view
- Multiple session start warnings in header

### Added
- Improved error logging for task assignments
- Better user feedback for comment sections
- Dynamic "Be the first to comment" button when no comments exist
- Threaded comments support with reply functionality
- Automatic display of existing comments without requiring click

### Changed
- Comments now show by default when they exist
- Session handling improved with new SessionHelper class
- Task assignment process now validates both task and user existence
