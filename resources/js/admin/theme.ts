import { createTheme, type MantineColorsTuple } from '@mantine/core';

// Same accent as the portfolio (#2ee6a6) so the admin feels like the site.
const mint: MantineColorsTuple = [
    '#e6fbf3',
    '#c9f5e3',
    '#95ebc9',
    '#5cf0bd',
    '#2ee6a6',
    '#1fd394',
    '#12b47f',
    '#0d9468',
    '#0a7654',
    '#065a40',
];

export const theme = createTheme({
    primaryColor: 'mint',
    colors: { mint },
    fontFamily: "'Manrope', ui-sans-serif, system-ui, sans-serif",
    headings: { fontFamily: "'Space Grotesk', system-ui, sans-serif", fontWeight: '700' },
    defaultRadius: 'md',
    primaryShade: { light: 6, dark: 4 },
});
