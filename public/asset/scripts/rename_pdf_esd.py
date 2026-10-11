import os
import fitz  # PyMuPDF: pip install pymupdf
import shutil

def is_pdf_scanned(pdf_path):
    """
    True  -> PDF scanné (image)
    False -> PDF texte (valide)
    """
    try:
        doc = fitz.open(pdf_path)
        for page in doc:
            text = page.get_text().strip()
            if text:
                return False
        return True
    except Exception as e:
        print(f"Erreur lecture PDF {pdf_path} : {e}")
        return None

def traiter_et_copier_pdf(pdf_path, dossier_cible, statut):
    dossier_src, nom_fichier = os.path.split(pdf_path)
    nom, ext = os.path.splitext(nom_fichier)

    nouveau_nom = f"{nom}-{statut}{ext}"
    chemin_cible = os.path.join(dossier_cible, nouveau_nom)

    # Gestion des doublons
    compteur = 1
    while os.path.exists(chemin_cible):
        nouveau_nom = f"{nom}-{statut}-{compteur}{ext}"
        chemin_cible = os.path.join(dossier_cible, nouveau_nom)
        compteur += 1

    shutil.copy2(pdf_path, chemin_cible)  # COPIE
    print(f"Copié : {pdf_path} → {chemin_cible}")

def traiter_repertoire(repertoire_source, repertoire_cible):
    os.makedirs(repertoire_cible, exist_ok=True)  # crée le dossier cible si absent

    for root, dirs, files in os.walk(repertoire_source):
        for file in files:
            if file.lower().endswith(".pdf"):
                chemin_pdf = os.path.join(root, file)

                resultat = is_pdf_scanned(chemin_pdf)

                if resultat is True:
                    traiter_et_copier_pdf(chemin_pdf, repertoire_cible, "(Scanned)")

                elif resultat is False:
                    traiter_et_copier_pdf(chemin_pdf, repertoire_cible, "Valide")

                else:
                    print(f"Ignoré : {file}")

# ============================
# PARAMÈTRES
# ============================
if __name__ == "__main__":
    dossier_source = r"/usr/esddata"     # dossier source sur le serveur du cenadi
    dossier_cible  = r"/usr/esddata/bonita_pdf"     # dossier de dépôt des copies renommées sur le serveur du cenadi

    traiter_repertoire(dossier_source, dossier_cible)
