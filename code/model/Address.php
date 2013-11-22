<?php
/**
 * Address model using a generic format for storing international addresses.
 * 
 * Typical Address Hierarcy:
 * 	Continent
 * 	Country
 * 	State / Province / Territory (Island?)
 * 	District / Suburb / County / City
 *		Code / Zip (may cross over the above)
 * 	Street / Road - name + type: eg Gandalf Cresent
 * 	(Premises/Building/Unit/Suite)
 * 		(Floor/Level/Side/Wing)
 * 	Number / Entrance / Room
 * 	Person(s), Company, Department
 *
 * Collection of international address formats:
 * @see http://bitboost.com/ref/international-address-formats.html
 * xAL address standard:
 * @see https://www.oasis-open.org/committees/ciq/ciq.html#6
 * Universal Postal Union addressing standards:
 * @see http://www.upu.int/nc/en/activities/addressing/standards.html
 */
class Address extends DataObject{

	static $db = array(
		'Country' 		=> 'ShopCountry',  //level1: Country = ISO 2-character country code
		'State' 			=> 'Varchar(100)', //level2: Locality, Administrative Area, State, Province, Territory, Island
		'City' 			=> 'Varchar(100)', //level3: Dependent Locality, City, Suburb, County, District
		'PostalCode' 	=> 'Varchar(20)',  //code: ZipCode, PostCode (could cross above levels within a country)
		
		'Address' 		=> 'Varchar(255)', //Number + type of thoroughfare/street. P.O. box
		'AddressLine2' => 'Varchar(255)', //Premises, Apartment, Building. Suite, Unit, Floor, Level, Side, Wing.

		'Latitude' 		=> 'Float(10,6)',  //GPS co-ordinates
		'Longitude' 	=> 'Float(10,6)',
		
		'Company'		=> 'Varchar(100)', //Business, Organisation, Group, Institution. 
		
		'FirstName' 	=> 'Varchar(100)', //Individual, Person, Contact, Attention
		'Surname' 		=> 'Varchar(100)',
		'Phone' 			=> 'Varchar(100)',
	);
	
	static $has_one = array(
		'Member' => 'Member'		
	);
	
	static $casting = array(
		'Country' => 'ShopCountry'	
	);
	
	static $required_fields = array(
		'Address',
		'City',
		'State',
		'Country'
	);
	
	/**
	 * Tub-titles for address fields that describe what they are for
	 * @var boolean
	 */
	static $show_form_hints = false;
	
	/**
	 * @todo: customise format and labels, based on passed locale
	 * @param unknown_type $nameprefix
	 */
	function getFormFields($nameprefix = ""){
		$countries = SiteConfig::current_site_config()->getCountriesList();
		$countryfield = new ReadonlyField($nameprefix."Country",_t('Address.COUNTRY','Country'));
		if(count($countries) > 1){
			$countryfield = new DropdownField($nameprefix."Country",_t('Address.COUNTRY','Country'), $countries, 'NZ');
		}
		else {
			$countryfield->setValue($countries[0]);
		}
		$fields = new FieldList(
			$phonefield = new TextField($nameprefix.'Phone', _t('Address.PHONE','Phone Number')),
			$addressfield = new TextField($nameprefix.'Address', _t('Address.ADDRESS','Address')),
			$address2field = new TextField($nameprefix.'AddressLine2', _t('Address.ADDRESSLINE2','&nbsp;')),
			$cityfield = new TextField($nameprefix.'City', _t('Address.CITY','City')),
			$statefield = new TextField($nameprefix.'State', _t('Address.STATE','State')),
			$postcodefield = new TextField($nameprefix.'PostalCode', _t('Address.POSTALCODE','Postal Code')),
			$countryfield
		);		
		if(self::$show_form_hints){
			$addressfield->setRightTitle(_t("Address.ADDRESSHINT","street / thoroughfare number, name, and type or P.O. Box"));
			$address2field->setRightTitle(_t("Address.ADDRESS2HINT","premises, building, apartment, unit, floor"));
			$cityfield->setRightTitle(_t("Address.CITYHINT","or suburb, county, district"));
			$statefield->setRightTitle(_t("Address.STATEHINT","or province, territory, island"));
		}
		$this->extend('updateFormFields',$fields,$nameprefix); 
		return $fields;
	}
	
	/**
	 * Get an array of fields that must be populated in a form.
	 * Required fields can be customised via self::$required_fields
	 */
	function getRequiredFields($nameprefix = ""){
		$fields = array();
		foreach(self::$required_fields as $field){
			$fields[] = $nameprefix.$field;
		}
		$this->extend('updateRequiredFields',$fields,$nameprefix);
		return $fields;
	}
	
	/**
	 * Produces a map for loading/saving form fields.
	 */
	function getFieldMap($prefix = ''){
		$map = $this->getFormFields()->saveableFields();
		foreach($map as $key => $value){
			$map[$prefix.$key] = $key;
			unset($map[$key]);
		}
		return $map;
	}
	
	/**
	 * Get full name associated with this Address
	 */
	function getName(){
		return implode('',array_filter(array(
			$this->FirstName,
			$this->Surname
		)));
	}
	
	/**
	 * Convert address to a single string.
	 */
	function toString($separator = ", "){
		$fields = array(
			'FirstName' => $this->FirstName,
			'Company' => $this->Company,
			'Surname' => $this->Surname,
			'Address' => $this->Address,
			'AddressLine2' => $this->AddressLine2,
			'City' => $this->City,
			'PostalCode' => $this->PostalCode,
			'State' => $this->State,
			'Country' => $this->Country,
			'Phone' => $this->Phone
		);
		$this->extend('updateToString',$fields);
		return implode($separator,array_filter($fields));
	}
	
	function forTemplate(){
		return $this->renderWith('Address');
	}
	
	/**
	 * Add alias setters for fields which are synonymous
	 */
	function setProvince($val){$this->State = $val;}
	function setTerritory($val){$this->State = $val;}
	function setIsland($val){$this->State = $val;}
	function setPostCode($val){$this->PostalCode = $val;}
	function setZipCode($val){$this->PostalCode = $val;}
	function setStreet($val){$this->Address = $val;}
	function setStreet2($val){$this->AddressLine2 = $val;}
	function setAddress2($val){$this->AddressLine2 = $val;}
	function setInstitution($val){$this->Company = $val;}
	function setBusiness($val){$this->Company = $val;}
	function setOrganisation($val){$this->Company = $val;}
	function setOrganization($val){$this->Company = $val;}
	
}