# Design System

## Direction

Sport editorial for cycling: energetic, direct, and rooted in Costa Rica's green roads. The memorable anchor is a full-bleed riding image framed by dark forest green, acid lime, and a restrained route-orange accent.

The design system keeps the riding imagery, typography, controls, and product cards consistent across the storefront.

## Typography

- **Barlow Condensed Bold** for display headings: tall, compact forms give the storefront an athletic voice.
- **DM Sans** for body and interface text: open letterforms keep prices, product details, and checkout controls legible.
- Both font families are self-hosted in `theme/ciclo-finca/assets/fonts/`; their SIL Open Font Licenses are included beside the files.

## Color Tokens

| Token | Value | Use |
| --- | --- | --- |
| Forest | `#16352C` | Header, hero overlay, major panels |
| Pine | `#285A43` | Links and secondary actions |
| Lime | `#D8F05A` | Primary action and energetic highlights |
| Route orange | `#E65A32` | Small accents and active markers |
| Chalk | `#F4F5EF` | Page background |
| Paper | `#FFFFFF` | Product cards and form surfaces |
| Ink | `#18231E` | Main text |
| Muted | `#56645C` | Secondary copy |
| Line | `#DDE3DA` | Borders and separators |

## Layout

- Full-width header and full-bleed homepage hero.
- Editorial content uses a centered wide container with consistent gutters.
- Product cards share image ratios and fixed content rhythm; purchase controls align to the card foot.
- Header links pair a recognizable icon with a visible label. Account and cart controls stay adjacent.
- Use a 4/8/12/16/24/32/48/64 spacing rhythm.

## Motion

- One short entrance sequence belongs to the hero and introduces the storefront.
- Hover motion is gated to fine pointers and communicates focus or interactivity.
- Button press feedback and card-image movement use transform and opacity with short, interruptible transitions.
- Reduced-motion preferences disable movement while keeping clear state feedback.

## Interaction and Accessibility

- Keep familiar shop, category, cart, and account conventions.
- Show cart quantity and current navigation state.
- Maintain visible focus indicators, descriptive link labels, and minimum 48px action targets.
- Keep titles, product names, prices, and purchase buttons aligned across catalog cards.
