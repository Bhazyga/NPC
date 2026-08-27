
# NPC — Services & Works Detail System

## Project

Nusantara Project Creative (NPC)

Website:

https://www.nusantaraprojectcreative.com/

Existing pages:

* `/services`
* `/works`

The project already has Services and Works.

IMPORTANT:

Do NOT rebuild the existing Services and Works system from zero.

First inspect the existing codebase, database structure, models, controllers, routes, views/components, and existing CRUD.

The objective is to evolve the existing system into a proper **Service Detail + Work Detail + Media Gallery + YouTube + Tags + Links** architecture.

---

# 01. CORE OBJECTIVE

When a visitor clicks a Service:

```text
Services
   ↓
Service Detail
   ↓
Description
Capabilities / Service Items
YouTube Video (optional)
Gallery (optional)
Related Works
Tags
External Links
CTA
```

When a visitor clicks a Work:

```text
Works
   ↓
Work Detail
   ↓
Project Description
Project Metadata
YouTube Video (optional)
Gallery
Related Services
Tags
External Links
```

The detail page must feel like a premium creative portfolio case-study page.

Do NOT make it look like an admin dashboard.

---

# 02. IMPORTANT NPC POSITIONING

Nusantara Project Creative is NOT primarily a generic entertainment/event agency.

A significant portion of NPC's activities and event-related work is connected to:

* Education
* Educational institutions
* Schools
* Universities
* Student activities
* Academic events
* Educational campaigns
* Seminars
* Workshops
* Competitions
* Community learning
* Youth programs
* Educational digital platforms
* Creative events with educational purposes

Therefore the content architecture must support education as a first-class category.

Do NOT hard-code NPC as:

"Music / Concert / Entertainment Agency"

The existing website already communicates broader creative services and event-related work. Preserve that flexibility.

---

# 03. EXISTING SERVICES

The existing website currently communicates six core services:

01 — Branding

02 — Graphic Design

03 — Web Development

04 — Motion Design

05 — Creative Campaign

06 — Content Creation

The existing service descriptions and service item concepts should be preserved unless the existing database already contains more accurate content.

Existing examples include:

### Branding

* Identity
* Logo
* Visual System
* Brand Voice
* Guideline

### Graphic Design

* Print
* Digital
* Typography
* Layout
* Illustration

### Web Development

* React
* Laravel
* UI/UX
* Animation
* CMS

### Motion Design

* 2D Animation
* After Effects
* Lottie
* 3D
* Reel

### Creative Campaign

* Strategy
* Copywriting
* Social
* Campaign

### Content Creation

* Photo
* Video
* Reels
* Scripting
* Editing

Do not duplicate these values blindly if they already exist in the database.

Inspect first.

---

# 04. SERVICE DETAIL PAGE

Every Service should have a readable detail page.

Suggested URL:

```text
/services/{slug}
```

Example:

```text
/services/branding
/services/graphic-design
/services/web-development
```

The slug must be generated and stored safely.

Prefer:

```text
unique slug
```

instead of using service names directly in URLs.

---

# 05. SERVICE DETAIL CONTENT

A Service should support:

* Title
* Slug
* Short description
* Long description
* Hero image
* Service items / capabilities
* Tags
* YouTube video
* Gallery
* External links
* Related Works
* SEO title
* SEO description
* Published status
* Sort/order
* Created timestamp
* Updated timestamp

---

# 06. SERVICE ITEMS

Service items should NOT be stored as a comma-separated string.

BAD:

```text
"Logo, Visual System, Brand Voice, Guideline"
```

Prefer a normalized structure.

Recommended:

```text
services
service_items
```

Relationship:

```text
Service
  hasMany
  ServiceItem
```

ServiceItem fields:

```text
id
service_id
title
description nullable
sort_order
created_at
updated_at
```

This allows future expansion.

Example:

```text
Branding
 ├── Logo
 ├── Visual System
 ├── Brand Voice
 └── Guideline
```

---

# 07. SERVICE YOUTUBE VIDEO

A service may optionally have a YouTube video.

Use:

```text
youtube_url nullable
```

Do NOT make it required.

If `youtube_url` is NULL:

DO NOT render:

* video container
* empty iframe
* "video unavailable"
* placeholder

Simply omit the video section.

If a valid YouTube URL exists:

Render a responsive embedded player.

Support common formats:

```text
https://www.youtube.com/watch?v=VIDEO_ID
https://youtu.be/VIDEO_ID
https://www.youtube.com/shorts/VIDEO_ID
```

Normalize the URL into a safe embed format.

Do not blindly inject arbitrary URLs into iframe src.

---

# 08. SERVICE GALLERY

Services can optionally have multiple images.

Do NOT store images like:

```text
image_1
image_2
image_3
image_4
```

inside the services table.

Use a dedicated media table.

Recommended:

```text
service_media
```

Fields:

```text
id
service_id
type
path
title nullable
alt_text nullable
caption nullable
sort_order
created_at
updated_at
```

For service gallery:

```text
type = image
```

If there are no images:

DO NOT render:

```text
Gallery
No images available
```

The entire gallery section should disappear.

---

# 09. WORK DETAIL PAGE

Every Work should have a detail page.

Suggested URL:

```text
/works/{slug}
```

Example:

```text
/works/kampanye-merek-serenade
```

A Work should behave like a case study.

---

# 10. WORK DATA

A Work should support:

* Title
* Slug
* Short description
* Long description
* Client
* Year
* Cover image
* Hero image
* Category
* Tags
* YouTube video
* Gallery
* External links
* Related Services
* Published status
* Featured status
* Sort/order
* SEO title
* SEO description
* Created timestamp
* Updated timestamp

Do not create unnecessary fields if the existing database already has equivalent fields.

Reuse existing structures where possible.

---

# 11. WORK METADATA

The Work detail page should have a clean metadata area.

Example:

```text
CLIENT
Nusantara.id

YEAR
2025

SERVICES
Web Development
UI/UX

CATEGORY
Digital / Education

TYPE
Educational Platform
```

Do not show metadata fields that are empty.

If Client is missing:

Do not display:

```text
CLIENT —
```

Simply hide that metadata item.

---

# 12. WORK DESCRIPTION

Separate:

```text
short_description
```

from:

```text
description
```

Short description:

Used for cards / previews / listing pages.

Long description:

Used for the Work detail page.

The long description should support readable editorial content.

Avoid putting giant walls of text into a single UI block.

Use typography hierarchy:

* Intro
* Problem / Context
* Approach
* Execution
* Result

Only render these subsections if content exists.

---

# 13. EDUCATIONAL EVENT / WORK CATEGORY

The system must support education-focused projects.

Recommended categories:

```text
Education
Branding
Digital Product
Campaign
Event
Community
Corporate
Creative
Social Impact
Entertainment
```

But do NOT hard-code these values into frontend components.

Prefer a database-backed taxonomy if the project already supports categories/tags.

---

# 14. TAG SYSTEM

Tags must be normalized.

DO NOT store:

```text
"education, event, branding, school"
```

as a single database field.

Use:

```text
tags
```

with pivot relationships.

Recommended:

```text
tags
```

Fields:

```text
id
name
slug
created_at
updated_at
```

Relationships:

```text
services <-> tags
works <-> tags
```

Recommended pivot tables:

```text
service_tag
work_tag
```

Each pivot:

```text
service_id
tag_id
```

or:

```text
work_id
tag_id
```

with indexes and unique constraints.

---

# 15. TAG EXAMPLES

Because NPC has a strong education/event orientation, examples include:

```text
Education
Educational Event
School
University
Student
Workshop
Seminar
Competition
Youth
Community
Campaign
Event
Branding
Digital
Web
UI/UX
Motion
Content
Production
```

These are examples only.

The admin must be able to create and manage tags.

---

# 16. EXTERNAL LINKS

Works and Services may contain external links.

Examples:

```text
Website
Instagram
Behance
YouTube
Registration
Project
Documentation
Demo
```

Do NOT create separate database columns for every possible platform.

Bad:

```text
instagram_url
behance_url
website_url
registration_url
demo_url
```

Prefer a normalized links table.

Recommended:

```text
links
```

Fields:

```text
id
linkable_type
linkable_id
label
url
type nullable
sort_order
created_at
updated_at
```

Use Laravel polymorphic relation:

```text
morphTo()
```

This allows both Services and Works to have links.

Example:

```text
Work
 ├── Website
 ├── YouTube
 ├── Registration
 └── Documentation
```

---

# 17. LINK VALIDATION

Validate URLs.

Use Laravel URL validation.

Do not allow arbitrary dangerous schemes.

Only allow appropriate protocols such as:

```text
https
http
```

If possible, prefer HTTPS.

---

# 18. MEDIA ARCHITECTURE

For Works, use a dedicated media table.

Recommended:

```text
work_media
```

Fields:

```text
id
work_id
type
path
title nullable
alt_text nullable
caption nullable
sort_order
is_featured
created_at
updated_at
```

Types can include:

```text
image
```

Future types can be added without redesigning the Work table.

---

# 19. GALLERY UX

The Work detail gallery should be:

* Clean
* Editorial
* Responsive
* Readable
* Easy to scan
* Visually dominant
* Not overcrowded

Desktop:

Use a dynamic editorial grid.

Example:

```text
┌───────────────────────────────┐
│                               │
│          IMAGE 01             │
│                               │
└───────────────────────────────┘

┌──────────────┐ ┌──────────────┐
│              │ │              │
│   IMAGE 02   │ │   IMAGE 03   │
│              │ │              │
└──────────────┘ └──────────────┘

┌───────────────────────────────┐
│                               │
│          IMAGE 04             │
│                               │
└───────────────────────────────┘
```

Do not force every image into the same aspect ratio.

Preserve original image composition where practical.

---

# 20. IMAGE LIGHTBOX

Clicking a gallery image should open a proper lightbox.

Requirements:

* Full-size image
* Caption if available
* Previous
* Next
* Close
* Keyboard support
* Mobile swipe if supported by existing frontend architecture

Do not reload the page.

---

# 21. YOUTUBE UX

If YouTube exists:

```text
VIDEO
[ Responsive YouTube Player ]
```

If YouTube does not exist:

The entire section does not exist.

No empty whitespace should be reserved.

Video must maintain responsive aspect ratio.

Preferred:

```text
16 / 9
```

Do not autoplay with sound.

Prefer privacy-enhanced YouTube embedding where possible.

---

# 22. WORK DETAIL LAYOUT

Recommended hierarchy:

```text
WORK DETAIL

01
CATEGORY / TAGS

PROJECT TITLE

Short description

Hero image


PROJECT INFORMATION

Client
Year
Services
Category


ABOUT THE PROJECT

Long description


VIDEO
(optional)


PROJECT GALLERY
(optional)


SERVICES USED

Related service cards


TAGS

#Education
#Event
#Branding


EXTERNAL LINKS
(optional)


NEXT PROJECT
```

The actual design should follow the existing NPC visual language.

Do not turn this into a generic Bootstrap case-study template.

---

# 23. SERVICE DETAIL LAYOUT

Recommended hierarchy:

```text
SERVICE DETAIL

01
SERVICE

TITLE

Short description

Hero visual


ABOUT THIS SERVICE

Long description


CAPABILITIES

Service items


VIDEO
(optional)


GALLERY
(optional)


SELECTED WORKS

Related works


TAGS


EXTERNAL LINKS
(optional)


START A PROJECT
```

---

# 24. RELATED WORKS

Services should be able to display related Works.

Relationship:

```text
services <-> works
```

Recommended pivot:

```text
service_work
```

Fields:

```text
service_id
work_id
sort_order nullable
```

Use many-to-many.

Example:

```text
Branding
 ├── Kampanye Merek Serenade
 ├── Identitas Visual Warung Emas
 └── Other Projects
```

---

# 25. DATABASE MIGRATIONS

Before creating migrations:

AUDIT the current schema.

If these tables already exist:

```text
services
works
```

DO NOT create duplicate tables.

Instead create incremental migrations.

Potential new migrations:

```text
create_service_items_table
create_tags_table
create_service_tag_table
create_work_tag_table
create_work_media_table
create_service_media_table
create_links_table
create_service_work_table
```

If the existing schema lacks fields such as:

```text
slug
description
youtube_url
cover_image
published_at
```

create ALTER migrations instead of rebuilding the table.

---

# 26. DATABASE BEST PRACTICES

Every foreign key must use proper constraints.

Prefer:

```php
$table->foreignId('service_id')
    ->constrained()
    ->cascadeOnDelete();
```

Use indexes on:

* slug
* foreign keys
* published status where useful
* sort order where useful

Slug should be unique where appropriate.

Use:

```php
$table->string('slug')->unique();
```

Do not use integer IDs in public URLs when a slug is appropriate.

---

# 27. CRUD ARCHITECTURE

Admin must support full CRUD.

## Services

```text
Create
Read
Update
Delete
```

Service CRUD must support:

* Title
* Slug
* Short description
* Description
* Hero image
* Service items
* YouTube
* Gallery
* Tags
* Links
* Related Works
* SEO
* Published status
* Sort order

---

## Works

CRUD must support:

* Title
* Slug
* Short description
* Description
* Client
* Year
* Category
* Cover image
* Hero image
* YouTube
* Gallery
* Tags
* Links
* Services
* Published status
* Featured
* Sort order
* SEO

---

# 28. FORM REQUEST VALIDATION

Do not put huge validation arrays directly inside controllers.

Use Form Requests.

Example:

```text
StoreServiceRequest
UpdateServiceRequest

StoreWorkRequest
UpdateWorkRequest
```

Validate:

* title
* slug
* URLs
* images
* descriptions
* year
* relationships
* status

Sanitize / normalize slugs.

---

# 29. CONTROLLER ARCHITECTURE

Do not put all business logic inside controllers.

Controllers should remain thin.

Use:

```text
Controller
   ↓
Form Request
   ↓
Model / Service Layer
   ↓
Database
```

If media processing becomes complex, extract it into a dedicated action/service class.

Example:

```text
CreateServiceAction
UpdateServiceAction
CreateWorkAction
UpdateWorkAction
```

Only introduce service classes where they actually reduce complexity.

Do not over-engineer.

---

# 30. MODEL RELATIONSHIPS

Expected relationships:

```php
Service
    hasMany(ServiceItem::class)
    hasMany(ServiceMedia::class)
    belongsToMany(Tag::class)
    belongsToMany(Work::class)
    morphMany(Link::class, 'linkable')
```

```php
Work
    hasMany(WorkMedia::class)
    belongsToMany(Service::class)
    belongsToMany(Tag::class)
    morphMany(Link::class, 'linkable')
```

```php
Tag
    belongsToMany(Service::class)
    belongsToMany(Work::class)
```

Use appropriate inverse relationships.

---

# 31. EAGER LOADING

Avoid N+1 queries.

For service detail:

```php
Service::with([
    'items',
    'media',
    'tags',
    'links',
    'works',
])->where('slug', $slug)->firstOrFail();
```

For work detail:

```php
Work::with([
    'media',
    'tags',
    'links',
    'services',
])->where('slug', $slug)->firstOrFail();
```

Only load fields/relationships actually required by the page where performance matters.

---

# 32. PUBLIC ROUTES

Recommended:

```text
GET /services
GET /services/{service:slug}

GET /works
GET /works/{work:slug}
```

Use route model binding by slug where appropriate.

Example:

```php
Route::get('/services/{service:slug}', ...);
Route::get('/works/{work:slug}', ...);
```

---

# 33. ADMIN ROUTES

Use appropriate admin middleware/authentication.

Recommended:

```text
/admin/services
/admin/services/create
/admin/services/{service}/edit

/admin/works
/admin/works/create
/admin/works/{work}/edit

/admin/tags
```

Do not expose CRUD routes publicly.

---

# 34. EMPTY STATE RULE

This is extremely important.

Never render empty UI sections.

Examples:

If no YouTube:

```text
DO NOT RENDER VIDEO SECTION
```

If no gallery:

```text
DO NOT RENDER GALLERY
```

If no external links:

```text
DO NOT RENDER LINKS
```

If no tags:

```text
DO NOT RENDER TAG SECTION
```

If no related Works:

```text
DO NOT RENDER RELATED WORKS
```

The page should naturally collapse.

---

# 35. SEO

Service and Work detail pages should support:

```text
seo_title
seo_description
```

Fallback:

```text
seo_title = title
seo_description = short_description
```

Generate meaningful page metadata.

Use canonical URLs where appropriate.

---

# 36. EDUCATION-FIRST CONTENT MODEL

Do not create an "education mode".

Education is a content category, not a separate website.

The system should allow a Work to be tagged:

```text
Education
Educational Event
University
Workshop
Campaign
Student
```

Example:

```text
Work:
"Platform Digital Nusantara.id"

Tags:
Education
Digital
Web Development
UI/UX
```

Another example:

```text
Work:
"Festival Kreativitas Pelajar"

Tags:
Education
Student
Event
Branding
Creative Campaign
```

This makes NPC capable of presenting educational projects without restricting other creative work.

---

# 37. IMPORTANT CONTENT DIRECTION

When generating example content or seed data:

Prioritize educational/event contexts.

Examples:

* School events
* University events
* Student festivals
* Academic conferences
* Workshops
* Seminars
* Competitions
* Educational campaigns
* Youth programs
* Community education
* Digital education platforms

But do NOT remove existing creative categories such as:

* Branding
* Corporate
* Digital
* Entertainment
* Community
* Content
* Motion

NPC remains a creative studio.

Education is a strong specialization/context, not the only service.

---

# 38. ACCESSIBILITY

Images must support:

```text
alt_text
```

YouTube iframe must have a meaningful title.

Gallery controls must be keyboard accessible.

Buttons must have readable labels.

Do not rely only on color to communicate state.

---

# 39. PERFORMANCE

Images should be:

* lazy loaded when appropriate
* responsive
* optimized
* served in appropriate dimensions

Do not load all full-resolution gallery images immediately if the page contains many images.

Use thumbnails for gallery previews where the existing infrastructure allows it.

---

# 40. SECURITY

Never trust:

* uploaded filenames
* external URLs
* HTML descriptions
* user-submitted metadata

Validate uploads.

Validate URLs.

If rich text is supported, sanitize HTML.

Do not directly output unsanitized user HTML.

YouTube IDs must be parsed safely.

---

# 41. MIGRATION ORDER

Recommended migration dependency order:

```text
1. services / existing schema audit

2. works / existing schema audit

3. service_items

4. tags

5. service_tag

6. work_tag

7. service_media

8. work_media

9. links

10. service_work
```

Foreign keys should always reference tables that already exist.

---

# 42. SEEDING

Create realistic seed data only if the project currently uses seeders.

Seed:

```text
6 core services
```

Use existing NPC service names.

Create example tags including education-related tags.

Create example Works based on the existing project data.

Do not invent fake client testimonials or fake project achievements.

Clearly mark development-only seed data if necessary.

---

# 43. ADMIN UX

The CRUD interface should remain simple.

Service form sections:

```text
Basic Information
Description
Hero Image
Service Items
YouTube
Gallery
Tags
Links
Related Works
SEO
Publishing
```

Work form:

```text
Basic Information
Project Information
Description
Hero / Cover
YouTube
Gallery
Services
Tags
Links
SEO
Publishing
```

Use tabs/accordions only if they improve usability.

Do not create a giant intimidating form.

---

# 44. FRONTEND DESIGN

The detail pages must follow the existing NPC design system.

Do not introduce:

* random colors
* generic gradients
* generic Bootstrap cards
* excessive rounded cards
* generic SaaS layouts
* excessive shadows

Maintain:

* NPC Navy
* NPC Gold
* existing typography
* existing motion
* existing editorial composition
* existing spacing system
* existing visual language

The detail page should feel like a natural extension of `/services` and `/works`.

---

# 45. DETAIL PAGE INTERACTION

When clicking:

```text
Service Card
```

navigate to:

```text
/services/{slug}
```

When clicking:

```text
Work Card
```

navigate to:

```text
/works/{slug}
```

Do not make the entire experience dependent on a modal.

A proper URL is important for:

* sharing
* SEO
* browser navigation
* bookmarking
* analytics
* discoverability

A modal/lightbox may still be used for gallery images.

---

# 46. FINAL QA

Before considering the feature complete, test:

## Services

* [ ] Service listing works
* [ ] Service detail works
* [ ] Slugs work
* [ ] Service items display
* [ ] YouTube displays only when present
* [ ] Gallery displays only when images exist
* [ ] Tags display correctly
* [ ] Links display correctly
* [ ] Related Works display correctly
* [ ] Empty sections collapse

## Works

* [ ] Work listing works
* [ ] Work detail works
* [ ] Slugs work
* [ ] Metadata displays
* [ ] Description displays
* [ ] YouTube displays only when present
* [ ] Gallery works
* [ ] Lightbox works
* [ ] Tags work
* [ ] Links work
* [ ] Related Services work

## Database

* [ ] No duplicate migrations
* [ ] No duplicate tables
* [ ] Foreign keys valid
* [ ] Unique constraints valid
* [ ] Pivot relationships valid
* [ ] Cascade behavior reviewed
* [ ] Indexes present
* [ ] Seeders work

## Security

* [ ] URLs validated
* [ ] Uploads validated
* [ ] HTML sanitized if applicable
* [ ] YouTube URLs normalized safely
* [ ] Admin routes protected

## Responsive

* [ ] Desktop
* [ ] Tablet
* [ ] Mobile

---

# FINAL RULE

Do not just "make a detail page".

Build a maintainable content architecture for NPC.

The system must allow NPC to grow from:

```text
Services + Works
```

into:

```text
Services
 ├── Details
 ├── Capabilities
 ├── Gallery
 ├── Video
 ├── Tags
 ├── Links
 └── Related Works

Works
 ├── Case Study
 ├── Gallery
 ├── Video
 ├── Tags
 ├── Links
 └── Related Services
```

The result must be production-quality, maintainable, scalable, accessible, secure, and visually consistent with the existing Nusantara Project Creative website.
