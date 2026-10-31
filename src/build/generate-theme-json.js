// generate-theme-json.js
// Node.js script to parse CSS custom-property tokens and generate theme.json
// for the WordPress block editor.
//
// Usage:
//   npm run generate-theme-json
//
// Reads the :root custom properties from src/css/tokens.css and generates
// theme.json with a color palette and font sizes for Gutenberg.
//
// Looks for:
// - Color variables: --col-{slug} (excluding aliases like --col-primary that
//   just point at another color — only "real" palette colors get listed)
// - Font size variables: --fs-*

const fs = require('fs');
const path = require('path');
const { readTokensFromFile } = require('./read-tokens');

const tokensFile = path.join(__dirname, '../css/tokens.css');
const themeJsonFile = path.join(__dirname, '../../theme.json');

function buildThemeJson(tokens) {
	// Palette colors: --col-{slug} whose value is a literal color, not a
	// var() alias (aliases like --col-primary: var(--col-blue-700) point at
	// one of the literal ones, so they'd just be a duplicate palette entry).
	const colors = Object.entries(tokens)
		.filter(([key, value]) => key.startsWith('col-') && !value.startsWith('var('))
		.map(([key, value]) => ({
			name: key.replace('col-', ''),
			slug: key.replace('col-', ''),
			color: value,
		}));

	const fontSizes = Object.entries(tokens)
		.filter(([key]) => key.startsWith('fs-'))
		.map(([key, value]) => {
			const slug = key.replace('fs-', '');
			return { name: slug, slug, size: value };
		});

	// Font families: --f-{slug} tokens, e.g. --f-display -> .has-display-font-family
	const fontFamilies = Object.entries(tokens)
		.filter(([key]) => /^f-/.test(key))
		.map(([key, value]) => ({ slug: key.replace('f-', ''), name: key.replace('f-', ''), fontFamily: value }));

	// Blocks with native background-colour support only offer the five brand surfaces
	// (palette entries for body-text tones and white stay out of the picker).
	const surfaces = ['nero', 'charcoal', 'sea', 'crusta', 'solitude'];
	const surfacePalette = colors.filter((c) => surfaces.includes(c.slug)).map((c) => ({ ...c, origin: 'theme' }));
	const blockSettings = {};
	fs.readdirSync(path.join(__dirname, '../../blocks'), { withFileTypes: true })
		.filter((d) => d.isDirectory() && fs.existsSync(path.join(__dirname, '../../blocks', d.name, 'block.json')))
		.forEach((d) => {
			const meta = JSON.parse(fs.readFileSync(path.join(__dirname, '../../blocks', d.name, 'block.json'), 'utf8'));
			if (meta.supports && meta.supports.color && meta.supports.color.background) {
				blockSettings[meta.name] = { color: { palette: surfacePalette, custom: false, customGradient: false, defaultPalette: false, defaultGradients: false } };
			}
		});

	return {
		$schema: 'https://schemas.wp.org/trunk/theme.json',
		version: 3,
		settings: {
			appearanceTools: true,
			// Blocks render their own full-width .container, so the editor's constrained layout
			// (which caps every block at contentSize) must allow the container width, not WP's default.
			layout: { contentSize: tokens['container-max-width'] || '1320px', wideSize: `${parseInt(tokens['container-max-width'] || '1320', 10) + 64}px` },
			color: {
				custom: false,
				customGradient: false,
				defaultPalette: false,
				defaultGradients: false,
				palette: colors.map((color) => ({ ...color, origin: 'theme' })),
			},
			typography: { customFontSize: false, defaultFontSizes: false, fontFamilies, fontSizes },
			blocks: blockSettings,
		},
	};
}

function main() {
	if (!fs.existsSync(tokensFile)) {
		console.error('Tokens file not found:', tokensFile);
		process.exit(1);
	}
	const tokens = readTokensFromFile(tokensFile);
	const themeJson = buildThemeJson(tokens);
	fs.writeFileSync(themeJsonFile, JSON.stringify(themeJson, null, 2));
	console.log('theme.json generated successfully.');
}

main();