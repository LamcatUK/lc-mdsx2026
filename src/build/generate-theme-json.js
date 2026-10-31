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

	return {
		$schema: 'https://schemas.wp.org/trunk/theme.json',
		version: 3,
		settings: {
			appearanceTools: true,
			layout: { contentSize: '760px', wideSize: tokens['container-max-width'] || '1320px' },
			color: {
				custom: false,
				customGradient: false,
				defaultPalette: false,
				defaultGradients: false,
				palette: colors.map((color) => ({ ...color, origin: 'theme' })),
			},
			typography: { customFontSize: false, defaultFontSizes: false, fontFamilies, fontSizes },
		},
		styles: {
			color: { background: 'var(--wp--preset--color--nero)', text: 'var(--wp--preset--color--solitude)' },
			typography: { fontFamily: 'var(--wp--preset--font-family--body)' },
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