# NPC Content Architecture

## Services & Works Detail System

**Project:** Nusantara Project Creative
**Purpose:** Content management architecture for Services, Works, Media, Tags, Links, and Relationships.

---

# 1. Content Architecture

```text
                        ┌──────────────┐
                        │    SERVICES  │
                        └──────┬───────┘
                               │
             ┌─────────────────┼─────────────────┐
             │                 │                 │
             ▼                 ▼                 ▼
       Service Items       Media/Gallery       Tags
             │                 │                 │
             │                 ▼                 │
             │             YouTube               │
             │                                   │
             └───────────────┬───────────────────┘
                             │
                             ▼
                       Related Works
                             │
                             ▼
                        ┌──────────┐
                        │  WORKS   │
                        └────┬─────┘
                             │
          ┌──────────────────┼──────────────────┐
          ▼                  ▼                  ▼
       Gallery            Tags               Links
          │                  │
          ▼                  │
       YouTube               │
                             │
                             ▼
                     Related Services
```

---

# 2. Core Tables

Existing core tables:

```text
services
works
```

Supporting tables:

```text
service_items
service_media
work_media

tags

service_tag
work_tag

service_work

links
```

---

# 3. Services

## services

Purpose:

Stores the primary NPC service definitions.

Recommended fields:

```text
id
title
slug
short_description
description
hero_image nullable
youtube_url nullable
seo_title nullable
seo_description nullable
is_published
sort_order
created_at
updated_at
```

Important:

If the existing `services` table already contains equivalent fields, reuse them.

Do not create duplicates.

---

# 4. Service Items

## service_items

Purpose:

Stores capabilities belonging to a service.

Example:

```text
Branding
 ├── Logo
 ├── Visual System
 ├── Brand Voice
 └── Guideline
```

Fields:

```text
id
service_id
title
description nullable
sort_order
created_at
updated_at
```

Relationship:

```text
services.id
        ↓
service_items.service_id
```

Delete behavior:

```text
CASCADE
```

---

# 5. Service Media

## service_media

Purpose:

Stores optional service gallery images.

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

Example:

```text
Branding
 ├── branding-process.jpg
 ├── branding-system.jpg
 └── branding-guideline.jpg
```

If no media exists:

Do not render the gallery.

---

# 6. Works

## works

Purpose:

Stores NPC portfolio projects.

Recommended fields:

```text
id
title
slug
short_description
description
client nullable
year nullable
category nullable
cover_image nullable
hero_image nullable
youtube_url nullable
seo_title nullable
seo_description nullable
is_published
is_featured
sort_order
created_at
updated_at
```

If `category` is already normalized in the existing application, do not duplicate it.

---

# 7. Work Media

## work_media

Purpose:

Stores gallery assets belonging to a Work.

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

Example:

```text
Platform Digital Nusantara.id
 ├── hero.jpg
 ├── dashboard.jpg
 ├── mobile.jpg
 └── campaign.jpg
```

---

# 8. Tags

## tags

Purpose:

Provides reusable content taxonomy.

Fields:

```text
id
name
slug
created_at
updated_at
```

Example tags:

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
Event
Branding
Digital
Web
UI/UX
Motion
Content
Production
Campaign
```

The actual tag list must remain manageable through the admin panel.

---

# 9. Service Tags

## service_tag

Purpose:

Many-to-many relationship between Services and Tags.

Fields:

```text
service_id
tag_id
```

Constraints:

```text
UNIQUE(service_id, tag_id)
```

Indexes:

```text
service_id
tag_id
```

---

# 10. Work Tags

## work_tag

Purpose:

Many-to-many relationship between Works and Tags.

Fields:

```text
work_id
tag_id
```

Constraints:

```text
UNIQUE(work_id, tag_id)
```

Indexes:

```text
work_id
tag_id
```

---

# 11. Service / Work Relationship

## service_work

Purpose:

Associates Works with the Services used to produce them.

Fields:

```text
service_id
work_id
sort_order nullable
```

Constraint:

```text
UNIQUE(service_id, work_id)
```

Example:

```text
Work:
Platform Digital Nusantara.id

Services:
- Web Development
- Graphic Design
```

---

# 12. Links

## links

Purpose:

Stores optional external links for Services and Works.

Use a polymorphic relationship.

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

Examples:

```text
Website
Demo
Registration
Documentation
Behance
Instagram
YouTube
Project
```

Do not create separate URL columns for every platform.

---

# 13. Laravel Relationships

## Service

```php
public function items()
{
    return $this->hasMany(ServiceItem::class)
        ->orderBy('sort_order');
}

public function media()
{
    return $this->hasMany(ServiceMedia::class)
        ->orderBy('sort_order');
}

public function tags()
{
    return $this->belongsToMany(Tag::class);
}

public function works()
{
    return $this->belongsToMany(Work::class)
        ->withPivot('sort_order')
        ->orderBy('pivot_sort_order');
}

public function links()
{
    return $this->morphMany(Link::class, 'linkable')
        ->orderBy('sort_order');
}
```

---

# 14. Work Relationships

```php
public function media()
{
    return $this->hasMany(WorkMedia::class)
        ->orderBy('sort_order');
}

public function services()
{
    return $this->belongsToMany(Service::class)
        ->withPivot('sort_order')
        ->orderBy('pivot_sort_order');
}

public function tags()
{
    return $this->belongsToMany(Tag::class);
}

public function links()
{
    return $this->morphMany(Link::class, 'linkable')
        ->orderBy('sort_order');
}
```

---

# 15. Tag Relationships

```php
public function services()
{
    return $this->belongsToMany(Service::class);
}

public function works()
{
    return $this->belongsToMany(Work::class);
}
```

---

# 16. Link Relationship

```php
public function linkable()
{
    return $this->morphTo();
}
```

This allows:

```text
Service → Links
Work → Links
```

without creating multiple link tables.

---

# 17. Recommended Migration Sequence

Run migrations in dependency order.

```text
1. Existing services table
2. Existing works table

3. service_items
4. tags
5. service_tag
6. work_tag
7. service_media
8. work_media
9. service_work
10. links
```

Before migration creation:

**Inspect existing schema first.**

---

# 18. Migration Principles

Use:

```php
$table->id();
```

Use:

```php
$table->foreignId('service_id')
    ->constrained()
    ->cascadeOnDelete();
```

Use:

```php
$table->string('slug')->unique();
```

Use nullable fields where content is genuinely optional.

Do not make:

```text
youtube_url
gallery
external links
captions
SEO fields
```

required.

---

# 19. YouTube Rules

Database:

```text
youtube_url nullable
```

Frontend:

```text
if youtube_url exists:
    render video

else:
    render nothing
```

Supported input:

```text
youtube.com/watch?v=
youtu.be/
youtube.com/shorts/
```

Normalize into a safe embed URL.

Never trust arbitrary iframe URLs.

---

# 20. Gallery Rules

Database:

```text
media records
```

Frontend:

```text
if media.count > 0:
    render gallery

else:
    render nothing
```

Do not render empty gallery headings.

---

# 21. Link Rules

Database:

```text
links
```

Frontend:

```text
if links.count > 0:
    render links

else:
    render nothing
```

Each link should have:

```text
label
url
type
```

Example:

```text
VIEW WEBSITE
VIEW PROJECT
WATCH VIDEO
REGISTER
```

---

# 22. Tag Rules

Database:

```text
tags
service_tag
work_tag
```

Frontend:

```text
if tags.count > 0:
    render tags
```

Tags should be visually subtle.

Do not make tags larger than the project title.

---

# 23. Service Detail

Route:

```text
/services/{service:slug}
```

Page structure:

```text
SERVICE
01

TITLE

Short Description

Hero


ABOUT

Long Description


CAPABILITIES

Service Items


VIDEO
if available


GALLERY
if available


SELECTED WORKS
if available


TAGS
if available


LINKS
if available


CTA
```

---

# 24. Work Detail

Route:

```text
/works/{work:slug}
```

Page structure:

```text
WORK
01

TITLE

Short Description

Hero


PROJECT INFORMATION

Client
Year
Category
Services


ABOUT THE PROJECT

Long Description


VIDEO
if available


GALLERY
if available


SERVICES USED
if available


TAGS
if available


LINKS
if available


NEXT PROJECT
```

---

# 25. Education-Oriented Taxonomy

Education must be represented naturally through tags/categories.

Recommended:

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
Community Learning
Academic
```

This allows NPC to communicate its educational/event focus without restructuring the entire website.

---

# 26. Example Educational Work

```text
Title:
Festival Kreativitas Pelajar

Category:
Education

Tags:
Education
Student
Event
Branding
Creative Campaign

Services:
Branding
Graphic Design
Creative Campaign
Content Creation
```

---

# 27. Example Educational Digital Work

```text
Title:
Platform Digital Nusantara.id

Category:
Education / Digital

Tags:
Education
Digital
Web
UI/UX

Services:
Web Development
Graphic Design
```

---

# 28. Content Philosophy

NPC content should communicate:

```text
Creative capability
+
Cultural identity
+
Digital execution
+
Educational impact
+
Event experience
```

Education is a major context.

It is not a restriction.

---

# 29. CRUD

## Services CRUD

```text
Create
Read
Update
Delete
```

### Create

Should support:

```text
Basic Information
Service Items
Hero
YouTube
Gallery
Tags
Links
Related Works
SEO
Publishing
```

### Update

Must preserve existing media unless explicitly removed.

### Delete

Deleting a Service should safely remove dependent:

```text
service_items
service_media
service_tag
service_work
links
```

according to foreign key / polymorphic cleanup strategy.

---

# 30. Works CRUD

### Create

Should support:

```text
Basic Information
Project Information
Hero / Cover
Description
YouTube
Gallery
Services
Tags
Links
SEO
Publishing
```

### Update

Existing media should not be destroyed automatically.

Only remove media explicitly selected by the administrator.

### Delete

Deleting a Work should safely clean:

```text
work_media
work_tag
service_work
links
```

---

# 31. Admin Form UX

Use clear sections.

## Service

```text
[ Basic Information ]

Title
Slug
Short Description
Description

[ Visual ]

Hero Image

[ Capabilities ]

Service Items

[ Media ]

YouTube
Gallery

[ Taxonomy ]

Tags

[ Relations ]

Related Works

[ Links ]

External Links

[ SEO ]

SEO Title
SEO Description

[ Publishing ]

Published
Sort Order
```

---

## Work

```text
[ Basic Information ]

Title
Slug
Short Description
Description

[ Project ]

Client
Year
Category

[ Visual ]

Cover
Hero

[ Media ]

YouTube
Gallery

[ Relations ]

Services

[ Taxonomy ]

Tags

[ Links ]

External Links

[ SEO ]

SEO Title
SEO Description

[ Publishing ]

Published
Featured
Sort Order
```

---

# 32. Performance

Use eager loading.

Avoid:

```text
N+1 queries
```

Service detail should load:

```text
items
media
tags
links
works
```

Work detail should load:

```text
media
services
tags
links
```

Use pagination on listing pages.

Do not load every gallery image for `/works`.

---

# 33. Security

Validate:

```text
URLs
uploads
slugs
text
HTML
YouTube URLs
```

If rich text is implemented:

```text
sanitize HTML
```

Never directly render arbitrary HTML from database content without sanitization.

---

# 34. SEO

Every Service:

```text
/services/{slug}
```

Every Work:

```text
/works/{slug}
```

must be individually indexable.

Use:

```text
seo_title
seo_description
canonical
Open Graph title
Open Graph description
Open Graph image
```

where the current application architecture supports it.

---

# 35. Final Architecture

The final system should look like:

```text
SERVICES
│
├── Branding
│   ├── Items
│   ├── Media
│   ├── YouTube
│   ├── Tags
│   ├── Links
│   └── Related Works
│
├── Graphic Design
├── Web Development
├── Motion Design
├── Creative Campaign
└── Content Creation


WORKS
│
├── Project A
│   ├── Description
│   ├── Gallery
│   ├── YouTube
│   ├── Tags
│   ├── Links
│   └── Services
│
├── Project B
└── Project C
```

---

# 36. Final Design Principle

The database should not dictate the visual design.

The database provides structured content.

The frontend transforms that content into an editorial experience.

The final result should feel:

**Nusantara Project Creative**

not:

**Generic CMS Portfolio Template**

The content model must remain flexible enough for:

* Education
* Educational Events
* Schools
* Universities
* Student Programs
* Workshops
* Seminars
* Competitions
* Community
* Corporate
* Branding
* Digital Products
* Campaigns
* Motion
* Content
* Entertainment

without requiring a database redesign later.
