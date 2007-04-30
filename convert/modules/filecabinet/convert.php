<?php

  /**
   * Copies documents files into file cabinet module
   *
   * @author Matthew McNaney <mcnaney at gmail dot com>
   * @version $Id$
   */

PHPWS_Core::initModClass('filecabinet', 'Document.php');

function convert()
{
    $aok = true;
    if (Convert::isConverted('filecabinet')) {
        return _('File Cabinet has already converted Documents files.');
    }

    $mod_list = PHPWS_Core::installModList();

    if (!in_array('filecabinet', $mod_list)) {
        return _('The File Cabinet module is not installed.');
    }

    $source_directory = PHPWS_HOME_DIR . 'files/filecabinet/';
    $directory_contents = PHPWS_File::readDirectory($source_directory,false, true);
    if (empty($directory_contents)) {
        return _('Copy all the files you need converted into your files/filecabinet/ directory.');
    }

    $docs = Convert::getSourceDB('mod_documents_docs');
    $files = Convert::getSourceDB('mod_documents_files');

    if (empty($docs) || empty($files)) {
        $content[] = _('Documents module not installed or empty.');
    } else {
        $all_docs = $docs->select();
        $all_files = $files->select();
        PHPWS_DB::disconnect();

        if (empty($all_docs) || empty($all_files)) {
            $content[] = _('Documents module did not contain any files.');
        } elseif(PHPWS_Error::logIfError($all_files) || PHPWS_Error::logIfError($all_files)) {
            $aok = false;
            $content[] =  _('An error occurred while accessing your Document tables.');
            
        } else {
            Convert::siteDB();

            PHPWS_Core::initModClass('filecabinet', 'Folder.php');

            foreach ($all_docs as $doc) {
                $folder = new Folder;
                $folder->ftype = DOCUMENT_FOLDER;
                $folder->setTitle(utf8_encode($doc['label']));
                $desc = utf8_encode($doc['description']);
                if (!empty($doc['full_text'])) {
                    $desc .= '<br /><br />' . utf8_encode($doc['full_text']);
                }
                $folder->setDescription($desc);
                $folder->public_folder = $doc['approved'];

                if (PHPWS_Error::logIfError($folder->save())) {
                    $content[] = _('Error when creating a new folder.');
                    $aok = false;
                    break;
                }

                $doc_to_folder[$doc['id']] = $folder;
            }

            if ($aok) {
                PHPWS_Core::initModClass('filecabinet', 'Document.php');
                foreach ($all_files as $file) {
                    $document = new PHPWS_Document;

                    if (!isset($doc_to_folder[$file['doc']])) {
                        continue;
                    } else {
                        $directory = $doc_to_folder[$file['doc']]->getFullDirectory();
                        $folder_id = $doc_to_folder[$file['doc']]->id;
                    }

                    if (!in_array($file['name'], $directory_contents)) {
                        $content[] = sprintf(_('Could not locate file: %s'), $file['name']);
                        continue;
                    }

                    $document->file_name      = $file['name'];
                    $document->file_directory = $directory;
                    $document->folder_id      = $folder_id;
                    $document->file_type      = $file['type'];
                    $document->size           = $file['size'];
                    $document->setTitle(utf8_encode($file['name']));
               
                    $result = $document->save(false);
                
                    if (PHPWS_Error::logIfError($result)) {
                        $aok = false;
                        $content[] = _('An error occurred while converting your old documents.');
                    } else {
                        $source_path = $source_directory . $document->file_name;
                        if (!@copy($source_path, $document->getPath())) {
                            $content[] = sprintf(_('Could not copy file %s to %s.'), $source_path, $document->getPath()); 
                        } else {
                            unlink($source_path);
                        }
                    }
                }
                $content[] = _('Documents module converted.');
            }
        }
    }

    if ($aok) {
        $db = Convert::getSourceDB('mod_phatfile_files');
        if (empty($db)) {
            $content[] = _('PhatFile module not installed.');
        } else {
            $phatfiles = $db->select();
            PHPWS_DB::disconnect();

            if (empty($phatfiles)) {
                $content[] = _('No PhatFile files were found.');
            } else {
                Convert::siteDB();
                $folder = new Folder;
                $folder->ftype = DOCUMENT_FOLDER;
                $folder->setTitle('Phatfile conversion');
                $folder->public_folder = 1;
                $result = $folder->save();
                $directory = $folder->getFullDirectory();
                $folder_id = $folder->id;

                foreach ($phatfiles as $file) {
                    $document = new PHPWS_Document;
                    if (!in_array($file['label'], $directory_contents)) {
                        $content[] = sprintf(_('Could not locate file: %s'), $file['label']);
                        continue;
                    }

                    $document->file_name      = $file['label'];
                    $document->file_directory = $directory;
                    $document->folder_id      = $folder_id;
                    $document->file_type      = $file['type'];
                    $document->size           = $file['size'];
                    if (!empty($file['description'])) {
                        $document->setDescription($file['description']);
                    }

                    $document->setTitle(utf8_encode($file['label']));
                    $result = $document->save(false);
                
                    if (PHPWS_Error::logIfError($result)) {
                        $aok = false;
                        $content[] = _('An error occurred while converting your old phatfiles.');
                    } else {
                        $source_path = $source_directory . $document->file_name;
                        if (!@copy($source_path, $document->getPath())) {
                            $content[] = sprintf(_('Could not copy file %s to %s.'), $source_path, $document->getPath()); 
                        } else {
                            unlink($source_path);
                        }
                    }
                }
            }
            $content[] = _('Phatfile files converted.');
        }
    }

    if ($aok) {
        Convert::addConvert('filecabinet');
        $content[] = _('File Cabinet converted.');
    } else {
        $content[] = _('An error occurred while trying to convert your old file modules.');
    }

    return implode('<br />', $content);
}
?>