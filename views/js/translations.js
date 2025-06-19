// translations.js


const translations = {
    en: {
        'invalid_amount_limit': 'The amount entered is not within the allowed range of 0-{amount} EUR.',
    },
    fr: {
        'invalid_amount_limit': 'Le montant saisi n’est pas dans la plage autorisée de 0 à {amount} EUR.',
    },
    it: {
        'invalid_amount_limit': 'L\'importo inserito non rientra nell\'intervallo consentito di 0-{amount} EUR.',
    },
    es: {
        'invalid_amount_limit': 'El importe introducido no está dentro del rango permitido de 0-{amount} EUR.',
    },
    de: {
        'invalid_amount_limit': 'Der eingegebene Betrag liegt nicht im zulässigen Bereich von 0-{amount} EUR.',
    },
    nl: {
        'invalid_amount_limit': 'Het ingevoerde bedrag valt niet binnen het toegestane bereik van 0-{amount} EUR.',
    }
};

function __(key, replacements = {}) {
    const lang = document.documentElement.lang || 'en';
    let translation = (translations[lang] && translations[lang][key]) || translations['en'][key] || key;

    for (const placeholder in replacements) {
        translation = translation.replace(`{${placeholder}}`, replacements[placeholder]);
    }

    return translation;
}