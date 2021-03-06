<?php namespace System\Models;

use Igniter\Flame\Database\Traits\Purgeable;
use Igniter\Flame\Translation\Models\Language;
use Illuminate\Support\Facades\Lang;

/**
 * Languages Model Class
 * @package System
 */
class Languages_model extends Language
{
    use Purgeable;

    public $purgeable = ['translations'];

    public $relation = [
        'hasMany' => [
            'translations' => ['System\Models\Translations_model', 'foreignKey' => 'locale', 'otherKey' => 'code', 'delete' => TRUE],
        ],
    ];

    /**
     *  List of variables that cannot be mass assigned
     * @var array
     */
    protected $fillable = ['code', 'name'];

    public static function applySupportedLanguages()
    {
        setting()->set('supported_languages', self::getDropdownOptions()->keys()->toArray());
    }

    public static function getDropdownOptions()
    {
        return self::isEnabled()->dropdown('name', 'code');
    }

    public static function listCloneableLanguages()
    {
        return self::isEnabled()->whereNull('original_id')->dropdown('name', 'idiom');
    }

    //
    // Events
    //

    protected function afterSave()
    {
        $this->restorePurgedValues();

        if (array_key_exists('translations', $this->attributes))
            $this->addTranslations((array)$this->attributes['translations']);
    }

    //
    // Scopes
    //

    /**
     * Scope a query to only include enabled language
     *
     * @param $query
     *
     * @return $this
     */
    public function scopeIsEnabled($query)
    {
        return $query->where('status', 1);
    }

    //
    // Helpers
    //

    public function isDefault()
    {
        return ($this->code == setting('default_language'));
    }

    public function listAllFiles()
    {
        traceLog('Method Languages_model::listAllFiles() has been deprecated. Use Translator loader instead.');
    }

    public function getLines($locale, $group, $namespace = null)
    {
        $lines = app('translation.loader')->load($locale, $group, $namespace);

        ksort($lines);

        return array_dot($lines);
    }

    public function getTranslations($group, $namespace = null)
    {
        return $this->getLines($this->code, $group, $namespace);
    }

    public function addTranslations($translations)
    {
        $languageId = $this->getKey();
        if (!is_numeric($languageId))
            return FALSE;

        foreach ($translations as $key => $translation) {
            preg_match('/^(.+)::(?:(.+?))\.(.+)+$/', $key, $matches);

            if (!$matches OR count($matches) !== 4)
                continue;

            [$code, $namespace, $group, $item] = $matches;

            $this->updateTranslation($group, $namespace, $item, $translation['translation']);
        }
    }

    public function updateTranslations($group, $namespace = null, array $lines = [])
    {
        return collect($lines)->map(function ($text, $key) use ($group, $namespace) {
            $this->updateTranslation($group, $namespace, $key, $text);

            return $text;
        })->filter()->toArray();
    }

    public function updateTranslation($group, $namespace, $key, $text)
    {
        $oldText = Lang::get("{$namespace}::{$group}.{$key}", [], $this->code);

        if (strcmp($text, $oldText) === 0)
            return FALSE;

        $translation = $this->translations()->firstOrNew([
            'group' => $group,
            'namespace' => $namespace,
            'item' => $key,
        ]);

        $translation->updateAndLock($text);
    }
}
