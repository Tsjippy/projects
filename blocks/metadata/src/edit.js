import { __ } from "@wordpress/i18n";
import "./editor.scss";
import { useSelect } from "@wordpress/data";
import { useEntityProp } from "@wordpress/core-data";
import {
  SelectControl,
  __experimentalInputControl as InputControl,
} from "@wordpress/components";
import { useBlockProps } from "@wordpress/block-editor";
import apiFetch from "@wordpress/api-fetch";
import { useState, useEffect } from "@wordpress/element";

const Edit = ({ setAttributes, attributes, context }) => {
  const { number, url, manager_user_id, manager_name, manager_tel, manager_email, ministry } = attributes;

  const blockProps = useBlockProps();
  const postType   = useSelect(
    (select) => select("core/editor").getCurrentPostType(),
    [],
  );

  const [meta, setMeta]             = useEntityProp("postType", postType, "meta");
  const [ministries, setMinistries] = useState([]);

  useEffect(() => {
    apiFetch({ path: "/tsjippy/v2/projects/ministries?slug=ministry"}).then( res => {

      let options = response.map((c) => ({ label: c.post_title, value: c.ID }));

      options.unshift({
        label: __("Please select a ministry", "tsjippy"),
        value: "",
      });

      setMinistries(options);
    })
  }, []);

  const updateMetaValue = (value, key) => {
    let newMeta = { ...meta };

    newMeta[`tsjippy_${key}`] = value;

    setMeta(newMeta);
  };

  return (
    <div {...blockProps}>
      <h2>{__("Project Details")}</h2>

      <InputControl
        isPressEnterToChange={true}
        label={__("Project number")}
        value={number}
        onChange={(value) => updateMetaValue(value, "number")}
      />

      <InputControl
        isPressEnterToChange={true}
        label={__("Manager name")}
        value={manager["name"]}
        onChange={(value) => updateMetaValue(value, "manager_name")}
      />

      <InputControl
        isPressEnterToChange={true}
        label={__("Phone number")}
        value={manager["tel"]}
        onChange={(value) => updateMetaValue(value, "manager_tel")}
      />

      <InputControl
        isPressEnterToChange={true}
        label={__("E-mail address")}
        value={manager["email"]}
        onChange={(value) => updateMetaValue(value, "manager_email")}
      />

      <InputControl
        isPressEnterToChange={true}
        label={__("Website url")}
        value={url}
        onChange={(value) => updateMetaValue(value, "url")}
      />

      <SelectControl
        __next40pxDefaultSize={true} 
        label="Ministry"
        value={ministry}
        options={ministries}
        onChange={(value) => updateMetaValue(value, "ministry")}
        __nextHasNoMarginBottom
      />
    </div>
  );
};

export default Edit;
